<?php

namespace App\Services\Gateways;

use App\Models\Payment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http; // Laravel's HTTP Client
use Illuminate\Support\Facades\Log; // For logging
use Illuminate\Support\Str;

class SslCommerzGateway extends Gateway implements PaymentGatewayInterface
{
    protected static $key = 'sslcommerz';
    protected static $image = 'sslcommerz.png'; // Make sure you have this image

    protected static $config = [
        'store_id' => 'Store ID',
        'store_passwd' => 'Store Password',
        'sandbox_mode' => 'Enable Sandbox Mode (yes/no)', // To switch between live and sandbox
    ];

    protected const SANDBOX_URL = 'https://sandbox.sslcommerz.com';
    protected const LIVE_URL = 'https://securepay.sslcommerz.com'; // Or their current live URL

    public function getSupportedCurrencies(): array
    {
        // SSLCommerz primarily processes in BDT but can display amounts in other currencies.
        // Check their latest documentation for the full list of display currencies they support.
        // The actual transaction might still be settled in BDT or your agreed currency.
        return ["BDT", "USD", "EUR", "GBP"];
    }

    protected function getApiBaseUrl(): string
    {
        $config = self::dbConfig();
        return (isset($config->sandbox_mode) && strtolower($config->sandbox_mode) === 'yes')
            ? self::SANDBOX_URL
            : self::LIVE_URL;
    }

    public function create(Payment $payment): string // Returns redirect URL or throws exception
    {
        $charge = $this->prepareCharge($payment);

        $config = self::dbConfig();
        if (!$config || !isset($config->store_id) || !isset($config->store_passwd)) {
            Log::error('SSLCommerz: Configuration missing (store_id or store_passwd).');
            throw new \Exception('SSLCommerz gateway not configured properly.');
        }

        $apiBaseUrl = $this->getApiBaseUrl();
        $endpoint = $apiBaseUrl . '/gwprocess/v4/api.php';

        // Unique transaction ID for this payment attempt
        // SSLCommerz tran_id must be unique and alphanumeric (max 30 chars).
        // We'll use the payment ID prefixed, and ensure it's unique for retries if needed.
        $transactionId = 'TXN' . $payment->id . Str::random(5); // Ensures some uniqueness for retries

        $postData = [
            'store_id' => $config->store_id,
            'store_passwd' => $config->store_passwd,
            'total_amount' => round($charge['amount'], 2),
            'currency' => $charge['currency'],
            'tran_id' => $transactionId, // Your unique transaction ID
            'success_url' => route('payment.notify', 'sslcommerz') . '?status=success',
            'fail_url' => route('payment.notify', 'sslcommerz') . '?status=fail',
            'cancel_url' => route('payment.notify', 'sslcommerz') . '?status=cancel',
            // 'ipn_url' => route('payment.notify', ['gateway' => self::$key, 'status' => 'ipn']), // Optional IPN

            // Customer Information (Required)
            // Try to get from $payment->user if available, otherwise use placeholders
            'cus_name' => $payment->user->name ?? 'Customer Name',
            'cus_email' => $payment->user->email ?? 'customer@example.com',
            'cus_add1' => $payment->user->address ?? 'Customer Address',
            'cus_city' => $payment->user->city ?? 'Customer City',
            'cus_state' => $payment->user->state ?? 'Customer State',
            'cus_postcode' => $payment->user->postcode ?? '12345',
            'cus_country' => $payment->user->country ?? 'Bangladesh', // Default or from user
            'cus_phone' => $payment->user->phone ?? '01xxxxxxxxx',

            // Product Information (Required)
            'product_name' => 'Payment for ' . generalSetting('site_title'),
            'product_category' => 'General',
            'product_profile' => 'general', // For digital goods/services use 'non-physical-goods'

            // Shipping Information (If applicable, for digital goods usually not needed)
            'shipping_method' => 'NO', // For digital goods. Use 'YES' for physical.
            // 'num_of_item' => 1,
            // 'ship_name' => 'Shipping Name',
            // 'ship_add1' => 'Shipping Address',
            // ... other shipping fields if shipping_method = YES

            // Optional parameters - use these to pass back your internal payment ID
            'value_a' => encrypt($payment->id), // Our internal payment ID encrypted
            // 'value_b' => '',
            // 'value_c' => '',
            // 'value_d' => '',
        ];

        $response = Http::asForm()->post($endpoint, $postData);
        $responseData = $response->json();


        if ($response->successful() && isset($responseData['status']) && $responseData['status'] === 'SUCCESS') {
            $payment->meta = array_merge($payment->meta ?? [], [
                'sslcz_sessionkey' => $responseData['sessionkey'] ?? null,
                'sslcz_tran_id' => $transactionId, // Our generated tran_id
            ]);
            $payment->transaction_no = $transactionId; // Or a dedicated field
            $payment->save();

            return redirect()->to($responseData['GatewayPageURL']); // Redirect user to this URL
        } else {
            $errorMessage = 'SSLCommerz: Payment initiation failed.';
            if (isset($responseData['failedreason'])) {
                $errorMessage .= ' Reason: ' . $responseData['failedreason'];
            } else if (!$response->successful()) {
                $errorMessage .= ' HTTP Error: ' . $response->status();
            }
            throw new \Exception($errorMessage);
        }
    }

    public function verify($request) // $request is Illuminate\Http\Request
    {
        $config = self::dbConfig();
        if (!$config || !isset($config->store_id) || !isset($config->store_passwd)) {
            return back()->withError('Gateway configuration error.');
        }

        // Get our internal payment ID from value_a
        $encryptedPaymentId = $request->input('value_a');
        if (!$encryptedPaymentId) {
            return back()->withError('Payment verification failed: Invalid request.');
        }

        try {
            $paymentId = decrypt($encryptedPaymentId);
        } catch (\Throwable $th) {
            return back()->withError('Payment verification failed: Invalid transaction data.');
        }

        $payment = Payment::find($paymentId);

        Auth::guard('web')->loginUsingId($payment->user_id);

        if (!$payment) {
            Log::error('SSLCommerz: Payment record not found for decrypted ID.', ['decrypted_id' => $paymentId]);
            return back()->withError('Payment record not found.');
        }

        // If payment already processed, prevent reprocessing
        if ($payment->status === 'success') {
            Log::info('SSLCommerz: Payment already marked as successful.', ['payment_id' => $payment->id]);
            return $this->paymentSuccess($payment); // Or redirect to a generic success page
        }


        $routeStatus = $request->status;

        if ($routeStatus === 'fail') {
            $payment->status = 'failed';
            $payment->save();
            return back()->withError('Payment failed at gateway. Reason: ' . $request->input('error', 'Unknown'));
        }

        if ($routeStatus === 'cancel') {
            $payment->status = 'cancelled';
            $payment->save();
            return back()->withInfo('Payment was cancelled.');
        }

        // Only proceed to validation if the user is returning to the 'success_url'
        if (!$request->has('val_id')) {
            return back()->withError('Invalid payment verification attempt.');
        }

        // **Perform Transaction Validation API Call**
        $valId = $request->input('val_id'); // Validation ID from SSLCommerz
        $apiBaseUrl = $this->getApiBaseUrl();
        $validationEndpoint = $apiBaseUrl . '/validator/api/validationserverAPI.php';

        $validationParams = [
            'val_id' => $valId,
            'store_id' => $config->store_id,
            'store_passwd' => $config->store_passwd,
            'format' => 'json' // Request JSON response
        ];

        $validationResponse = Http::get($validationEndpoint, $validationParams);
        $validationData = $validationResponse->json();

        if (
            $validationResponse->successful() &&
            isset($validationData['status']) &&
            ($validationData['status'] === 'VALID' || $validationData['status'] === 'VALIDATED')
        ) {
            if ($validationData['tran_id'] != ($payment->meta['sslcz_tran_id'] ?? $payment->transaction_no)) {
                Log::error('SSLCommerz: Transaction ID mismatch during validation.', [
                    'expected' => $payment->meta['sslcz_tran_id'] ?? $payment->transaction_no,
                    'received' => $validationData['tran_id'],
                    'payment_id' => $payment->id
                ]);
                $payment->status = 'failed'; 
                $payment->save();
                return back()->withError('Payment verification failed: Transaction ID mismatch.');
            }

            // SSLCommerz might return amount like "10.00". Cast to float for comparison.
            info($validationData);
            $validatedAmount = (float) ($validationData['currency_amount'] ?? $validationData['store_amount'] ?? 0); // store_amount is amount after their commission
            $originalAmount = round((float) ($payment->meta['gateway_amount'] ?? $payment->amount), 2);

            // Allow for small discrepancies if store_amount is used, or ensure you check 'amount' (total amount)
            // This depends on what 'amount' field SSLCommerz returns in validation for total paid by customer
            if (abs($validatedAmount - $originalAmount) > 0.01) { // Compare amounts
                Log::error('SSLCommerz: Amount mismatch during validation.', [
                    'expected' => $originalAmount,
                    'received' => $validatedAmount,
                    'payment_id' => $payment->id
                ]);
                $payment->status = 'failed'; // Or "validation_failed"
                $payment->save();
                return back()->withError('Payment verification failed: Amount mismatch.');
            }

            $expectedCurrency = strtoupper($payment->meta['gateway_currency'] ?? $payment->currency);

            if (strtoupper($validationData['currency_type']) !== $expectedCurrency) {
                Log::error('SSLCommerz: Currency mismatch during validation.', [
                    'expected' => $expectedCurrency,
                    'received' => $validationData['currency'],
                    'payment_id' => $payment->id
                ]);
                $payment->status = 'failed'; // Or "validation_failed"
                $payment->save();
                return back()->withError('Payment verification failed: Currency mismatch.');
            }

            // All checks passed
            $payment->paid_at = now();
            $payment->status = 'success';
            // Store validation data if needed
            $payment->meta = array_merge($payment->meta ?? [], ['sslcz_validation' => $validationData]);
            $payment->save();

            Log::info('SSLCommerz: Payment verified and marked as success for payment ID ' . $payment->id);
            return $this->paymentSuccess($payment); // Call your common success handler

        } else {
            $errorMessage = 'SSLCommerz: Payment validation failed.';
            if (isset($validationData['status'])) {
                $errorMessage .= ' Status: ' . $validationData['status'];
            }
            if (isset($validationData['error'])) {
                $errorMessage .= ' Error: ' . $validationData['error'];
            } else if (!$validationResponse->successful()) {
                $errorMessage .= ' HTTP Error: ' . $validationResponse->status();
            }

            Log::error($errorMessage, ['payment_id' => $payment->id, 'validation_response' => $validationData]);
            $payment->status = 'failed'; // Or a special "validation_failed" status
            $payment->save();
            return back()->withError($errorMessage);
        }
    }
}

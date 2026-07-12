<?php

namespace App\Services\Gateways;

use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class FlutterwaveGateway extends Gateway implements PaymentGatewayInterface
{
    protected static $key = 'flutterwave';
    protected static $image = 'flutterwave.png';

    protected static $config = [
        'public_key' => 'Flutterwave Public Key',
        'secret_key' => 'Flutterwave Secret Key',
        'sandbox_mode' => 'Enable Sandbox Mode (yes/no)',
    ];

    public function getSupportedCurrencies(): array
    {
        return ["NGN", "USD", "EUR", "GBP", "KES", "GHS", "ZAR"];
    }

    protected function getApiBaseUrl(): string
    {
        $config = self::dbConfig();
        $isSandbox = isset($config->sandbox_mode) && strtolower($config->sandbox_mode) === 'yes';
        return $isSandbox ? 'https://api.flutterwave.com/v3/' : 'https://api.flutterwave.com/v3/';
    }

    public function create(Payment $payment): string
    {
        $charge = $this->prepareCharge($payment);

        $config = self::dbConfig();
        if (!$config || !isset($config->secret_key)) {
            Log::error('Flutterwave: Missing configuration.');
            throw new \Exception('Flutterwave gateway not configured properly.');
        }

        $txRef = 'FLW' . $payment->id . Str::random(5);
        $postData = [
            'tx_ref' => $txRef,
            'amount' => round($charge['amount'], 2),
            'currency' => $charge['currency'],
            'payment_type' => 'card',
            'redirect_url' => route('payment.notify', ['gateway' => self::$key, 'trx' => encrypt($payment->id)]),
            'customer' => [
                'email' => $payment->user->email ?? 'guest@example.com',
                'name'  => $payment->user->name ?? 'Guest',
            ],
            'meta' => [
                'payment_id' => $payment->id
            ]
        ];

        $response = Http::withToken($config->secret_key)
            ->post($this->getApiBaseUrl() . 'payments', $postData);

        if ($response->successful() && isset($response['data']['link'])) {
            $payment->transaction_id = $txRef;
            $payment->meta = array_merge($payment->meta ?? [], [
                'flutterwave_tx_ref' => $txRef,
            ]);
            $payment->save();
            return $response['data']['link']; // Redirect user to Flutterwave payment page
        }

        Log::error('Flutterwave: Payment creation failed', ['response' => $response->body()]);
        throw new \Exception('Flutterwave payment initiation failed.');
    }

    public function verify($request)
    {
        $paymentIdEnc = $request->trx ?? null;
        try {
            $id = decrypt($paymentIdEnc);
        } catch (\Throwable $th) {
            return back()->withError('Payment Failed');
        }

        $payment = Payment::find($id);
        if (!$payment) {
            return back()->withError('Payment not found');
        }

        $config = self::dbConfig();
        $txRef = $payment->transaction_id;

        $response = Http::withToken($config->secret_key)
            ->get($this->getApiBaseUrl() . 'transactions/verify_by_reference?tx_ref=' . $txRef);

        if ($response->successful() && isset($response['data']['status']) && $response['data']['status'] === 'successful') {
            $payment->paid_at = now();
            $payment->status = 'success';
            $payment->meta = array_merge($payment->meta ?? [], ['flutterwave_response' => $response['data']]);
            $payment->save();
            return $this->paymentSuccess($payment);
        }

        Log::error('Flutterwave: Verification failed', ['response' => $response->body()]);
        return back()->withError('Payment verification failed.');
    }
}

<?php

namespace App\Services\Gateways;

use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class KhaltiGateway extends Gateway implements PaymentGatewayInterface
{
    protected static $key = 'khalti';
    protected static $image = 'khalti.png';

    protected static $config = [
        'secret_key' => 'Khalti Secret Key',
        'public_key' => 'Khalti Public Key',
        'sandbox_mode' => 'Enable Sandbox Mode (yes/no)',
    ];

    public function getSupportedCurrencies(): array
    {
        return ["NPR"];
    }

    protected function getApiBaseUrl(): string
    {
        $config = self::dbConfig();
        $isSandbox = isset($config->sandbox_mode) && strtolower($config->sandbox_mode) === 'yes';
        return $isSandbox ? 'https://khalti.com/api/v2/' : 'https://khalti.com/api/v2/';
    }

    public function create(Payment $payment): string
    {
        $charge = $this->prepareCharge($payment);

        $config = self::dbConfig();
        if (!$config || !isset($config->secret_key)) {
            Log::error('Khalti: Missing configuration.');
            throw new \Exception('Khalti gateway not configured properly.');
        }

        $txRef = 'KHALTI' . $payment->id . Str::random(5);

        $postData = [
            'return_url' => route('payment.notify', ['gateway' => self::$key, 'trx' => encrypt($payment->id)]),
            'amount' => (int) round($charge['amount'] * 100), // Khalti expects amount in paisa
            'product_identity' => $payment->id,
            'product_name' => 'Payment to ' . generalSetting('site_title'),
            'customer_email' => $payment->user->email ?? 'guest@example.com',
            'customer_name' => $payment->user->name ?? 'Guest',
        ];

        // Khalti checkout URL for redirect
        $payment->transaction_id = $txRef;
        $payment->meta = array_merge($payment->meta ?? [], [
            'khalti_tx_ref' => $txRef,
        ]);
        $payment->save();

        $checkoutUrl = 'https://khalti.com/payment/checkout/' . $txRef; 
        return $checkoutUrl;
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

        $gatewayAmount = $payment->meta['gateway_amount'] ?? $payment->amount;

        $response = Http::withHeaders([
            'Authorization' => 'Key ' . $config->secret_key,
        ])->post($this->getApiBaseUrl() . 'payment/verify/', [
            'token' => $txRef,
            'amount' => (int) round($gatewayAmount * 100),
        ]);

        if ($response->successful() && isset($response['idx'])) {
            $payment->paid_at = now();
            $payment->status = 'success';
            $payment->meta = array_merge($payment->meta ?? [], ['khalti_response' => $response->json()]);
            $payment->save();
            return $this->paymentSuccess($payment);
        }

        Log::error('Khalti: Verification failed', ['response' => $response->body()]);
        return back()->withError('Payment verification failed.');
    }
}

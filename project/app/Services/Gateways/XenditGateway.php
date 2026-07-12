<?php

namespace App\Services\Gateways;

use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class XenditGateway extends Gateway implements PaymentGatewayInterface
{
    protected static $key = 'xendit';
    protected static $image = 'xendit.png';

    protected static $config = [
        'api_key' => 'Xendit API Key',
        'sandbox_mode' => 'Enable Sandbox Mode (yes/no)',
    ];

    public function getSupportedCurrencies(): array
    {
        return ["IDR", "PHP", "USD"];
    }

    protected function getApiBaseUrl(): string
    {
        $config = self::dbConfig();
        // $isSandbox = isset($config->sandbox_mode) && strtolower($config->sandbox_mode) === 'yes';
        $isSandbox = true;
        return $isSandbox ? 'https://api.xendit.co/v2/' : 'https://api.xendit.co/v2/';
    }

    public function create(Payment $payment): string
    {
        $charge = $this->prepareCharge($payment);

        $config = self::dbConfig();
        if (!$config || !isset($config->api_key)) {
            Log::error('Xendit: Missing configuration.');
            throw new \Exception('Xendit gateway not configured properly.');
        }

        $transactionId = 'XENDIT' . $payment->id . Str::random(5);

        $postData = [
            'external_id' => $transactionId,
            'amount' => round($charge['amount'], 2),
            'payer_email' => $payment->user->email ?? 'guest@example.com',
            'description' => 'Payment to ' . generalSetting('site_title'),
            'currency' => $charge['currency'],
            'callback_url' => route('payment.notify', ['key' => self::$key, 'trx' => encrypt($payment->id)]),
        ];

        $response = Http::withBasicAuth($config->api_key, '')
            ->post($this->getApiBaseUrl() . 'invoices', $postData);

        if ($response->successful() && isset($response['invoice_url'])) {
            $payment->transaction_no = $transactionId;
            $payment->meta = array_merge($payment->meta ?? [], [
                'xendit_invoice_id' => $response['id'],
            ]);
            $payment->save();
            return redirect()->away($response['invoice_url']);
        }

        Log::error('Xendit: Payment creation failed', ['response' => $response->body()]);
        throw new \Exception('Xendit payment initiation failed.');
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
        $invoiceId = $payment->meta['xendit_invoice_id'] ?? null;

        if (!$invoiceId) {
            return back()->withError('Payment verification failed: Missing Xendit invoice ID.');
        }

        $response = Http::withBasicAuth($config->api_key, '')
            ->get($this->getApiBaseUrl() . 'invoice/' . $invoiceId);

        if ($response->successful() && isset($response['status']) && strtolower($response['status']) === 'paid') {
            $payment->paid_at = now();
            $payment->status = 'success';
            $payment->meta = array_merge($payment->meta ?? [], ['xendit_response' => $response->json()]);
            $payment->save();
            return $this->paymentSuccess($payment);
        }

        Log::error('Xendit: Verification failed', ['response' => $response->body()]);
        return back()->withError('Payment verification failed.');
    }
}

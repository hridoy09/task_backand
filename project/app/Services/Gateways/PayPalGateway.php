<?php

namespace App\Services\Gateways;

use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;

class PayPalGateway extends Gateway implements PaymentGatewayInterface
{
    protected static $key = 'paypal';
    protected static $image = 'paypal.png';

    protected static $config = [
        'client_id' => 'PayPal Client ID',
        'client_secret' => 'PayPal Client Secret',
        'sandbox_mode' => 'Enable Sandbox Mode (yes/no)',
    ];

    public function getSupportedCurrencies(): array
    {
        return ["USD", "EUR", "GBP", "AUD", "CAD"];
    }

    protected function getClient(): PayPalHttpClient
    {
        $config = self::dbConfig();
        $isSandbox = isset($config->sandbox_mode) && strtolower($config->sandbox_mode) === 'yes';

        $environment = $isSandbox
            ? new SandboxEnvironment($config->client_id, $config->client_secret)
            : new ProductionEnvironment($config->client_id, $config->client_secret);

        return new PayPalHttpClient($environment);
    }

    public function create(Payment $payment): string
    {
        $charge = $this->prepareCharge($payment);

        $client = $this->getClient();

        $request = new OrdersCreateRequest();
        $request->prefer('return=representation');
        $request->body = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'reference_id' => 'PU-' . $payment->id,
                    'amount' => [
                        'value' => number_format($charge['amount'], 2, '.', ''),
                        'currency_code' => $charge['currency'],
                    ]
                ]
            ],
            'application_context' => [
                'cancel_url' => route('payment.notify', ['gateway' => self::$key, 'status' => 'cancel']),
                'return_url' => route('payment.notify', ['gateway' => self::$key, 'status' => 'success']),
            ]
        ];

        try {
            $response = $client->execute($request);
            $approvalUrl = collect($response->result->links)->firstWhere('rel', 'approve')->href ?? null;

            if (!$approvalUrl) {
                Log::error('PayPal: Approval URL not found.', (array)$response);
                throw new \Exception('PayPal payment initiation failed.');
            }

            $payment->transaction_id = $response->result->id;
            $payment->meta = array_merge($payment->meta ?? [], [
                'paypal_order_id' => $response->result->id,
            ]);
            $payment->save();

            return $approvalUrl;
        } catch (\Throwable $th) {
            Log::error('PayPal: Create payment failed', ['error' => $th->getMessage()]);
            throw new \Exception('PayPal payment initiation failed: ' . $th->getMessage());
        }
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

        $client = $this->getClient();
        $orderId = $payment->transaction_id;

        $captureRequest = new OrdersCaptureRequest($orderId);
        $captureRequest->prefer('return=representation');

        try {
            $response = $client->execute($captureRequest);
            if (strtolower($response->result->status) === 'completed') {
                $payment->paid_at = now();
                $payment->status = 'success';
                $payment->meta = array_merge($payment->meta ?? [], ['paypal_response' => $response->result]);
                $payment->save();
                return $this->paymentSuccess($payment);
            } else {
                return back()->withError('Payment not completed. Status: ' . $response->result->status);
            }
        } catch (\Throwable $th) {
            Log::error('PayPal: Verification failed', ['error' => $th->getMessage()]);
            return back()->withError('Payment verification failed: ' . $th->getMessage());
        }
    }
}

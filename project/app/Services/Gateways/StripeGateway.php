<?php

namespace App\Services\Gateways;

use App\Models\Payment;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class StripeGateway extends Gateway implements PaymentGatewayInterface
{
    protected static $key = 'stripe';

    protected static $image = 'stripe.png';

    protected static $config = [
        'api_key' => 'Api Key',
        'secret_key' => 'Secret Key',
    ];

    public function getSupportedCurrencies(): array
    {
        return ["TRY", "USD", "EUR", "GBP", "RUB", "CHF", "NOK"];
    }

    public function create(Payment $payment): string
    {
        $charge = $this->prepareCharge($payment);

        Stripe::setApiKey(self::dbConfig()?->secret_key);

        $session = Session::create([
            'mode' => 'payment',
            'success_url' => route('payment.notify', 'stripe') . '?trx=' . encrypt($payment->id),
            'line_items' => [
                [
                    'quantity' => 1,
                    'price_data' => [
                        'currency' => strtolower($charge['currency']),
                        'unit_amount' => (int) round($charge['amount'] * 100), // convert to smallest unit
                        'product_data' => ['name' => 'Payment to ' . generalSetting('site_title')]
                    ]
                ]
            ],
        ]);

        $payment->meta = array_merge($payment->meta ?? [], [
            'stripe_session_id' => $session->id,
        ]);
        $payment->save();

        return redirect()->away($session->url);
    }

    public function verify($request)
    {
        $trx = $request->trx;

        try {
            $id = decrypt($trx);
        } catch (\Throwable $th) {
            return back()->withError('Payment Failed');
        }

        $payment = Payment::where('id', $id)->first();

        if (!$payment) {
            return back()->withError('Payment not found');
        }

        Stripe::setApiKey(self::dbConfig()?->secret_key);

        $session = Session::retrieve($payment->meta['stripe_session_id']);

        if ($session->payment_status === 'paid') {
            $payment->paid_at = now();
            $payment->status = 'success';
            $payment->save();

            return $this->paymentSuccess($payment);
        }

        return back()->withError('Payment not completed');
    }

}

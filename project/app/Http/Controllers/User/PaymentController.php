<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Helpers\GatewayHelper;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Services\GatewayFactory;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function paymentHistory() 
    {
        $title = 'Payment History';

        $payments = auth()->user()->payments()->paginate();
        
        return theme('user.payment.history', compact('title', 'payments'));
    }
    
    public function newPayment(Request $request)
    {
        $title = 'New Payment';

        $systemHelper = app(\App\Helpers\SystemHelper::class);
        $allCurrencies = collect($systemHelper->currencies());

        $currencyCatalog = $allCurrencies
            ->mapWithKeys(function ($currency) {
                $code = $currency['code'] ?? null;
                if (!$code) {
                    return [];
                }

                $currency['symbol_decoded'] = html_entity_decode($currency['symbol'] ?? $code, ENT_QUOTES, 'UTF-8');

                return [$code => $currency];
            });

        $baseCurrencyCode = generalSetting('currency') ?? 'USD';
        $baseCurrency = $currencyCatalog->get($baseCurrencyCode, [
            'code' => $baseCurrencyCode,
            'symbol' => $baseCurrencyCode,
            'symbol_decoded' => $baseCurrencyCode,
            'name' => $baseCurrencyCode,
        ]);

        $paymentGateways = PaymentGateway::active()
            ->with('currencies')
            ->get()
            ->map(function ($gateway) use ($baseCurrencyCode) {
                $profile = [
                    'code' => $baseCurrencyCode,
                    'rate' => 1,
                    'base_currency' => $baseCurrencyCode,
                ];

                if (!$gateway->manual) {
                    $definition = GatewayHelper::paymentGateways($gateway->key, true);
                    if ($definition && isset($definition['class']) && method_exists($definition['class'], 'currencyProfile')) {
                        $profile = $definition['class']::currencyProfile();
                    }
                } elseif ($gateway->currency) {
                    $profile['code'] = $gateway->currency;
                }

                $gateway->setAttribute('charge_currency_code', $profile['code']);
                $gateway->setAttribute('charge_currency_rate', (float) $profile['rate']);
                $gateway->setAttribute('charge_base_currency', $profile['base_currency']);

                return $gateway;
            })
            ->values();

        return theme('user.payment.new', [
            'title' => $title,
            'paymentGateways' => $paymentGateways,
            'currencyCatalog' => $currencyCatalog,
            'baseCurrency' => $baseCurrency,
        ]);
    }

    public function notify(Request $request, $key)
    {
        $class = \App\Helpers\GatewayHelper::paymentGateways($key)['class'];

        return (new $class())->verify($request);
    }

    public function paymentInsert(Request $request)
    {
        $request->validate([
            'amount'               => 'required|numeric|gt:0',
            'payment_gateway_id'   => 'required|exists:payment_gateways,id'
        ]);

        $paymentGateway = PaymentGateway::where('status', 1)->findOrFail($request->payment_gateway_id);

        $gatewayCode = $paymentGateway->key;

        $user = auth()->user();

        // payment log
        $payment                     = new Payment();
        $payment->user_id            = $user->id;
        $payment->status             = 'pending';
        $payment->amount             = $request->amount;
        $payment->currency           = generalSetting('currency');
        $payment->transaction_no     = generateTransactionId();
        $payment->payment_gateway_id = $request->payment_gateway_id;
        $payment->method             = $gatewayCode;
        $payment->save();

        if ($user->email) {
            sendTemplatedNotification(
                $user->email,
                'PAYMENT_PENDING',
                [
                    'user_name' => $user->name,
                    'payment_amount' => format_amount_with_currency($payment->amount, $payment->currency),
                    'payment_currency' => $payment->currency,
                    'payment_method' => ucfirst($payment->method),
                    'transaction_no' => $payment->transaction_no,
                    'payment_created_at' => $payment->created_at?->toDayDateTimeString() ?? now()->toDayDateTimeString(),
                    'payment_details_url' => route('user.payment.history'),
                ]
            );
        }

        $adminRecipients = admin_notification_recipients();
        if (!empty($adminRecipients)) {
            sendTemplatedNotification(
                $adminRecipients,
                'ADMIN_PAYMENT_ALERT',
                [
                    'user_name' => $user->name,
                    'payment_amount' => format_amount_with_currency($payment->amount, $payment->currency),
                    'payment_currency' => $payment->currency,
                    'payment_method' => ucfirst($payment->method),
                    'transaction_no' => $payment->transaction_no,
                    'payment_status' => __('Pending'),
                    'payment_created_at' => $payment->created_at?->toDayDateTimeString() ?? now()->toDayDateTimeString(),
                    'payment_admin_url' => route('admin.payment.list'),
                ]
            );
        }

        $gateway = GatewayFactory::make($gatewayCode);
        return $gateway->create($payment);
    }

    public function paymentSuccess(Request $request)
    {
        $payment = Payment::with('user')->findOrFail($request->pid);

        if ($payment->status === 'success') {
            return redirect()->route('user.dashboard')->with('info', 'Already processed');
        }

        $gateway = GatewayFactory::make($payment->method);

        if ($gateway->verify($payment)) {
            $payment->status = 'success';
            $payment->save();

            $user = auth()->user();

            $before = $user->balance;
            $after  = $before + $payment->amount;

            $user->balance += $payment->amount;
            $user->save();

            $transaction = $user->transactions()->create([
                'payment_id'     => $payment->id,
                'trx'            => $payment->transaction_no ?? strtoupper(uniqid('TRX')),
                'currency'       => $payment->currency,
                'details'        => 'Payment via ' . ucfirst($payment->method),
                'trx_type'       => 'credit', // since it’s adding balance
                'amount'         => $payment->amount,
                'balance_before' => $before,
                'balance_after'  => $after,
                'remark'         => 'Payment successful',
                'category'       => 'deposit',
            ]);

            if ($user->email) {
                sendTemplatedNotification(
                    $user->email,
                    'PAYMENT_SUCCESS',
                    [
                        'user_name' => $user->name,
                        'payment_amount' => format_amount_with_currency($payment->amount, $payment->currency),
                        'payment_currency' => $payment->currency,
                        'payment_method' => ucfirst($payment->method),
                        'transaction_no' => $payment->transaction_no,
                        'payment_processed_at' => now()->toDayDateTimeString(),
                        'balance_before' => format_amount_with_currency($before, $payment->currency),
                        'balance_after' => format_amount_with_currency($after, $payment->currency),
                        'payment_receipt_url' => route('payment.history'),
                    ]
                );

                sendTemplatedNotification(
                    $user->email,
                    'TRANSACTION_CREATED',
                    [
                        'user_name' => $user->name,
                        'trx' => $transaction->trx,
                        'trx_type' => ucfirst($transaction->trx_type),
                        'amount' => format_amount_with_currency($transaction->amount, $transaction->currency),
                        'currency' => $transaction->currency,
                        'balance_before' => format_amount_with_currency($transaction->balance_before, $transaction->currency),
                        'balance_after' => format_amount_with_currency($transaction->balance_after, $transaction->currency),
                        'transaction_details' => $transaction->details,
                        'transaction_date' => $transaction->created_at?->toDayDateTimeString() ?? now()->toDayDateTimeString(),
                    ]
                );
            }

            $adminRecipients = admin_notification_recipients();
            if (!empty($adminRecipients)) {
                sendTemplatedNotification(
                    $adminRecipients,
                    'ADMIN_PAYMENT_ALERT',
                    [
                        'user_name' => $user->name,
                        'payment_amount' => format_amount_with_currency($payment->amount, $payment->currency),
                        'payment_currency' => $payment->currency,
                        'payment_method' => ucfirst($payment->method),
                        'transaction_no' => $payment->transaction_no,
                        'payment_status' => __('Success'),
                        'payment_created_at' => $payment->created_at?->toDayDateTimeString() ?? now()->toDayDateTimeString(),
                        'payment_admin_url' => route('admin.payment.list'),
                    ]
                );
            }
            
            return redirect()->route('user.dashboard')->with('success', 'Payment successful!');
        }

        if ($payment->user?->email) {
            sendTemplatedNotification(
                $payment->user->email,
                'PAYMENT_FAILED',
                [
                    'user_name' => $payment->user->name,
                    'payment_amount' => format_amount_with_currency($payment->amount, $payment->currency),
                    'payment_currency' => $payment->currency,
                    'payment_method' => ucfirst($payment->method),
                    'transaction_no' => $payment->transaction_no,
                    'failure_reason' => __('Payment verification failed.'),
                    'payment_created_at' => $payment->created_at?->toDayDateTimeString() ?? now()->toDayDateTimeString(),
                ]
            );
        }

        $adminRecipients = admin_notification_recipients();
        if (!empty($adminRecipients)) {
            sendTemplatedNotification(
                $adminRecipients,
                'ADMIN_PAYMENT_ALERT',
                [
                    'user_name' => $payment->user?->name ?? 'N/A',
                    'payment_amount' => format_amount_with_currency($payment->amount, $payment->currency),
                    'payment_currency' => $payment->currency,
                    'payment_method' => ucfirst($payment->method),
                    'transaction_no' => $payment->transaction_no,
                    'payment_status' => __('Failed'),
                    'payment_created_at' => $payment->created_at?->toDayDateTimeString() ?? now()->toDayDateTimeString(),
                    'payment_admin_url' => route('admin.payment.list'),
                ]
            );
        }

        return redirect()->route('user.dashboard')->with('error', 'Payment failed or incomplete.');
    }
}

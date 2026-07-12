<?php

namespace App\Helpers;

use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Models\PaymentGatewayCurrency;
use stdClass;

trait GatewayHelper
{
    public static function dbConfig()
    {
        $model = PaymentGateway::where('key', static::$key)->first();

        return $model?->config ?? new stdClass;
    }

    public static function filledConfig()
    {
        $model = PaymentGateway::where('key', static::$key)->first();

        if (!$model)
            return static::$config;

        static::$staticConfig = static::$config;

        static::$config = $model->config;

        return static::getConfigInput(true);
    }


    public static function getConfigInput($returnFromDb = false)
    {
        $inputs = [];

        foreach (($returnFromDb ? static::$staticConfig : static::$config) as $confKey => $confLabel) {
            $label = $returnFromDb ? static::$staticConfig[$confKey] : '';

            $value = $returnFromDb ? static::$config?->$confKey : '';

            $inputs[] = '<div class="form-group">
                <label for="' . htmlspecialchars($confKey) . '" class="form-label">' . __($label) . '</label>
                <input type="text" class="form-control" value="' . htmlspecialchars($value) . '" name="config[' . htmlspecialchars($confKey) . ']" />
            </div>';
        }

        return $inputs;
    }

    public static function paymentGateways($key = null, $fromDb = false, $createIfNotExists = false)
    {
        $classes = getClassesInNamespace('App\Services\Gateways');

        $gateways = [];

        foreach ($classes as $gatewayClassName) {
            $class = "App\\Services\\Gateways\\$gatewayClassName";

            $gateways[] = [
                'key' => $class::getKey(),
                'image' => $class::getImage(),
                'config' => $class::getConfig(),
                'class' => $class
            ];

            if (!$fromDb) {
                $gateways['config_input'] = $class::getConfigInput();
            }
        }

        if ($createIfNotExists) {
            self::createIfNotExists($gateways);
        }

        if ($key) {
            return collect($gateways)->where('key', $key)->first();
        }

        return $gateways;
    }

    public static function getKey()
    {
        return static::$key;
    }

    public static function getImage()
    {
        return static::$image;
    }

    public static function getConfig()
    {
        return static::$config;
    }

    public static function gatewayModel(bool $withCurrencies = false)
    {
        $query = PaymentGateway::query()->where('key', static::$key);

        if ($withCurrencies) {
            $query->with(['currencies' => function ($builder) {
                $builder->orderByDesc('is_default')->orderBy('currency_code');
            }]);
        }

        return $query->first();
    }

    protected function resolveGatewayCurrency(): array
    {
        $gateway = static::gatewayModel(true);

        $supportedCurrencies = method_exists($this, 'getSupportedCurrencies')
            ? $this->getSupportedCurrencies()
            : [];

        $currencies = collect($gateway?->currencies ?? []);

        $defaultCurrency = $currencies->first(function ($currency) use ($supportedCurrencies) {
            if (!($currency?->is_default ?? false)) {
                return false;
            }

            return empty($supportedCurrencies) || in_array($currency->currency_code, $supportedCurrencies, true);
        });

        if (!$defaultCurrency) {
            $defaultCurrency = $currencies->first(function ($currency) use ($supportedCurrencies) {
                return empty($supportedCurrencies) || in_array($currency->currency_code, $supportedCurrencies, true);
            });
        }

        if ($defaultCurrency) {
            $rate = (float) ($defaultCurrency->rate ?? 1);

            return [
                'code' => $defaultCurrency->currency_code,
                'rate' => $rate > 0 ? $rate : 1.0,
            ];
        }

        $fallbackCode = $supportedCurrencies[0] ?? (generalSetting('currency') ?? 'USD');

        return [
            'code' => $fallbackCode,
            'rate' => 1.0,
        ];
    }

    public static function currencyProfile(): array
    {
        $instance = new static();
        $currency = $instance->resolveGatewayCurrency();

        $baseCurrency = generalSetting('currency') ?? $currency['code'];

        return [
            'code'          => $currency['code'],
            'rate'          => $currency['rate'],
            'base_currency' => $baseCurrency,
        ];
    }

    protected function prepareCharge(Payment $payment): array
    {
        $gatewayCurrency = $this->resolveGatewayCurrency();
        $baseCurrency = generalSetting('currency') ?? $gatewayCurrency['code'];

        $rate = $gatewayCurrency['rate'] > 0 ? $gatewayCurrency['rate'] : 1.0;
        $convertedAmount = round($payment->amount * $rate, 2);

        $payment->meta = array_merge($payment->meta ?? [], [
            'base_amount'      => $payment->amount,
            'base_currency'    => $baseCurrency,
            'gateway_currency' => $gatewayCurrency['code'],
            'gateway_amount'   => $convertedAmount,
            'gateway_rate'     => $rate,
        ]);
        $payment->save();

        return [
            'amount'        => $convertedAmount,
            'currency'      => $gatewayCurrency['code'],
            'rate'          => $rate,
            'base_currency' => $baseCurrency,
        ];
    }

    protected function paymentSuccess(Payment $payment)
    {
        /** add balance to user account adn you can do more things from here you should after a successfull payment ... */
        $this->addBalanceToUser($payment);

        return to_route('user.payment.history')->withSuccess(__('Payment successfull'));
    }

    private function addBalanceToUser(Payment $payment)
    {
        $user = $payment->user;
        
        $before = $user->balance;
        $after  = $before + $payment->amount;

        $user->balance += $payment->amount;
        $user->save();
        
        $user->balance += $payment->amount;
        $user->save();

        $user->transactions()->create([
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
    }

    private static function createIfNotExists($gatewaysFromClass)
    {
        foreach ($gatewaysFromClass as $gatewayFromClass) {
            if (empty($gatewayFromClass['key'])) {
                continue;
            }

            $dbEntry = PaymentGateway::firstOrCreate(
                ['key' => $gatewayFromClass['key']],
                [
                    'name' => ucfirst($gatewayFromClass['key']),
                    'image' => $gatewayFromClass['image'],
                    'config' => array_combine(
                        array_keys($gatewayFromClass['config']),
                        array_map(fn() => '-------------', $gatewayFromClass['config'])
                    ),
                    'instruction' => '',   
                    'manual'      => 0     
                ]
            );

            if ($dbEntry->wasRecentlyCreated) {
                $currency = generalSetting('currency') ?? 'USD';

                PaymentGatewayCurrency::firstOrCreate(
                    [
                        'payment_gateway_id' => $dbEntry->id,
                        'currency_code'      => $currency,
                    ],
                    [
                        'rate'       => 1,
                        'is_default' => true,
                    ]
                );

                if (!$dbEntry->currency) {
                    $dbEntry->currency = $currency;
                    $dbEntry->save();
                }
            }
        }
    }
}

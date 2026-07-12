<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\GatewayHelper;
use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Services\FileManager;
use App\Services\GatewayFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentGatewayController extends Controller
{
    public function list()
    {
        goIfUserCan('view-payment-gateways');
        $title = 'Payment Gateways';
        GatewayHelper::paymentGateways(null, false, true);
        $paymentGateways = PaymentGateway::orderBy('id', 'desc')->get();
        return view('admin.payment_gateway.list', compact('title', 'paymentGateways'));
    }


    public function changeStatus($key)
    {
        goIfUserCan('save-payment-gateways');

        $paymentGateway = PaymentGateway::where('key', $key)->first();
        if(!$paymentGateway) {
            return response()->json([
                'status'=>'error',
                'message'=>__('Payment gateway not found')
            ]);
        }
        $paymentGateway->status = $paymentGateway->status == 0 ? 1 : 0;
        $paymentGateway->save();

        return response()->json([
            'status'=>'success',
            'message'=>__('Payment gateway status changed')
        ]);
        
    }
    
public function edit($key)
{
    goIfUserCan('save-payment-gateways');

    $title = 'Edit Payment Gateway';

    $paymentGateway = PaymentGateway::with('currencies')->where('key', $key)->firstOrFail();


    // Build gateway instance
    $gateway = GatewayFactory::make($paymentGateway->key);
    $supportedCurrencies = $gateway->getSupportedCurrencies();
    $baseCurrency = generalSetting('currency') ?? 'USD';

    // Get old currencies from session or database
    $oldCurrencies = old('currencies');
    $currencyRows = collect();
    $defaultCurrency = old('default_currency', $paymentGateway->currency);

    if (is_array($oldCurrencies)) {
        // Build currency rows from old form data
        $currencyRows = collect($oldCurrencies)->map(function ($row) {
            return (object) [
                'currency_code' => $row['code'] ?? null,
                'rate' => $row['rate'] ?? null,
                'is_default' => false,
            ];
        });
        $defaultCurrency = old('default_currency');
    } else {
        // Load from DB
        $currencyRows = $paymentGateway->currencies ?? collect();
    }

    // Filter duplicate currency codes
    $currencyRows = $currencyRows
        ->unique('currency_code')
        ->filter(fn($c) => !empty($c->currency_code))
        ->values();

    // If empty and gateway supports currencies, preload first
    if ($currencyRows->isEmpty() && !empty($supportedCurrencies)) {
        $currencyRows = collect([
            (object) [
                'currency_code' => $supportedCurrencies[0],
                'rate' => 1,
                'is_default' => true,
            ],
        ]);
        $defaultCurrency = $supportedCurrencies[0];
    }

    return view('admin.payment_gateway.edit', compact(
        'title',
        'paymentGateway',
        'gateway',
        'supportedCurrencies',
        'currencyRows',
        'defaultCurrency',
        'baseCurrency'
    ));
}


    public function save(Request $request, $key = null)
    {
        goIfUserCan('save-payment-gateways');

        if ($key) {
            $paymentGateway = PaymentGateway::where('key', $key)->firstOrFail();
        } else {
            $paymentGateway = new PaymentGateway();
        }

        $gatewayDefinition = null;
        $supportedCurrencies = [];


            $gatewayDefinition = GatewayHelper::paymentGateways($paymentGateway->key ?? $key, true);
            if ($gatewayDefinition && isset($gatewayDefinition['class'])) {
                $supportedCurrencies = (new $gatewayDefinition['class']())->getSupportedCurrencies();
            }
        

        $rules = [
            'name'         => 'required|max:255|unique:payment_gateways,name,' . $key . ',key',
            'short_desc'   => 'nullable|max:255',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            'config'       => 'nullable',
            'is_test_mode' => 'required|in:0,1',
        ];

        if ($supportedCurrencies) {
            $rules['currencies'] = 'required|array|min:1';
            $rules['currencies.*.code'] = 'required|in:' . implode(',', $supportedCurrencies);
            $rules['currencies.*.rate'] = 'required|numeric|gt:0';
            $rules['default_currency'] = 'required|in:' . implode(',', $supportedCurrencies);
        } else {
            $rules['currencies'] = 'nullable|array';
            $rules['currencies.*.code'] = 'nullable|string';
            $rules['currencies.*.rate'] = 'nullable|numeric|gt:0';
            $rules['default_currency'] = 'nullable|string';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($supportedCurrencies) {
            $validator->after(function ($validator) use ($request) {
                $currencies = collect($request->input('currencies', []))
                    ->filter(fn ($row) => filled($row['code'] ?? null));

                $codes = $currencies->pluck('code');

                if ($codes->unique()->count() !== $codes->count()) {
                    $validator->errors()->add('currencies', __('Duplicate currencies are not allowed.'));
                }

                $defaultCurrency = $request->input('default_currency');
                if (!$defaultCurrency || !$codes->contains($defaultCurrency)) {
                    $validator->errors()->add('default_currency', __('Please select a default currency.'));
                }
            });
        }

        $validator->validate();

        $paymentGateway->name = $request->name;
        $paymentGateway->key  = str($request->name)->slug();

        if ($request->hasFile('image')) {
            $paymentGateway->image = FileManager::uploadToAssets(
                $request->image, 
                filePath('paymentGateway'), 
                $paymentGateway->image, 
                handleResize('paymentGateway')
            );
        }

        $config                       = $request->config;
        $paymentGateway->config       = $config;
        $paymentGateway->short_desc   = $request->short_desc;
        $paymentGateway->is_test_mode = $request->is_test_mode;
        $paymentGateway->save();

        if ($supportedCurrencies) {
            $submittedCurrencies = collect($request->input('currencies', []))
                ->map(fn ($row) => [
                    'code' => $row['code'] ?? null,
                    'rate' => isset($row['rate']) ? (float) $row['rate'] : null,
                ])
                ->filter(fn ($row) => $row['code'] && $row['rate']);

            $defaultCurrency = $request->input('default_currency');

            $paymentGateway->currencies()
                ->whereNotIn('currency_code', $submittedCurrencies->pluck('code'))
                ->delete();

            foreach ($submittedCurrencies as $currencyRow) {
                $paymentGateway->currencies()->updateOrCreate(
                    ['currency_code' => $currencyRow['code']],
                    [
                        'rate'       => round($currencyRow['rate'], 12),
                        'is_default' => $currencyRow['code'] === $defaultCurrency,
                    ]
                );
            }

            $paymentGateway->currency = $defaultCurrency;
            $paymentGateway->save();
        }


        return to_route('admin.payment_gateway.list')->withSuccess(__('Payment gateway saved successfully.'));
    }
}

@extends('theme::user.layouts.main')

@section('content')
@php
    $baseCode = $baseCurrency['code'] ?? generalSetting('currency');
    $baseSymbol = $baseCurrency['symbol_decoded'] ?? html_entity_decode($baseCurrency['symbol'] ?? $baseCode, ENT_QUOTES, 'UTF-8');

    $catalogCollection = $currencyCatalog instanceof \Illuminate\Support\Collection
        ? $currencyCatalog
        : collect($currencyCatalog ?? []);

    $currencyMetadata = $catalogCollection->mapWithKeys(function ($currency, $code) {
        $symbol = $currency['symbol_decoded'] ?? html_entity_decode($currency['symbol'] ?? $code, ENT_QUOTES, 'UTF-8');

        return [
            $code => [
                'symbol' => $symbol,
                'name'   => $currency['name'] ?? $code,
            ],
        ];
    });

    $formatRate = function ($value) {
        if ($value === null) {
            return '0';
        }

        $formatted = rtrim(rtrim(number_format($value, 12, '.', ''), '0'), '.');

        return $formatted === '' ? '0' : $formatted;
    };

    $chargeCurrencyList = collect($paymentGateways ?? [])
        ->pluck('charge_currency_code')
        ->filter()
        ->unique()
        ->map(function ($code) use ($currencyMetadata) {
            $meta = $currencyMetadata->get($code, ['symbol' => $code, 'name' => $code]);
            return [
                'code' => $code,
                'name' => $meta['name'] ?? $code,
                'symbol' => $meta['symbol'] ?? $code,
            ];
        })
        ->values();
@endphp

<div class="container py-5">
    <div class="text-center hero-section mb-5">
        <span class="badge text-bg-light hero-badge">@lang('Payment Center')</span>
        <h1 class="display-5 fw-bold mt-3">@lang('Secure Payment')</h1>
        <p class="text-muted lead">@lang('Choose your preferred payment method and enter the amount you wish to add.')</p>
        <div class="d-inline-flex flex-wrap gap-4 justify-content-center hero-metrics">
            <span class="text-muted small">
                <span class="metric-dot"></span>
                <strong>@lang('Base Currency')</strong>:
                {{ $baseSymbol }} {{ $baseCode }}
            </span>
            <span class="text-muted small">
                <span class="metric-dot"></span>
                @lang('All payment sessions are fully encrypted')
            </span>
        </div>
    </div>

    <div class="row g-4 align-items-start">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-4 payment-workflow">
                <div class="card-body p-4 p-md-5">

                    <div class="stepper mb-4">
                        <div class="step active">
                            <span class="step-index">1</span>
                            <div>
                                <h6 class="mb-0 fw-semibold">@lang('Select Gateway')</h6>
                                <small class="text-muted">@lang('Pick any supported provider below.')</small>
                            </div>
                        </div>
                        <div class="step">
                            <span class="step-index">2</span>
                            <div>
                                <h6 class="mb-0 fw-semibold">@lang('Enter Amount')</h6>
                                <small class="text-muted">@lang('Amount is entered in :code', ['code' => $baseCode])</small>
                            </div>
                        </div>
                        <div class="step">
                            <span class="step-index">3</span>
                            <div>
                                <h6 class="mb-0 fw-semibold">@lang('Review & Pay')</h6>
                                <small class="text-muted">@lang('We summarize charges before redirecting.')</small>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('user.payment.insert') }}" method="POST">
                        @csrf

                        <div class="selection-panel">
                            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-3 gap-3">
                                <div>
                                    <h4 class="fw-semibold mb-1">@lang('Select Payment Gateway')</h4>
                                    <small class="text-muted">@lang('Supported currencies are shown under each provider.')</small>
                                </div>
                                <div class="search-box flex-grow-1 flex-md-grow-0">
                                    <input type="text" id="gateway-search" class="form-control" placeholder="@lang('Search for a payment method...')">
                                </div>
                            </div>

                            <div class="row g-3" id="payment-gateway-list">
                                @foreach ($paymentGateways as $gateway)
                                    @php
                                        $chargeCode = $gateway->charge_currency_code ?? $baseCode;
                                        $chargeRate = $gateway->charge_currency_rate ?? 1;
                                        $chargeMeta = $currencyMetadata->get($chargeCode, ['symbol' => $chargeCode, 'name' => $chargeCode]);
                                        $chargeSymbol = $chargeMeta['symbol'];
                                        $chargeName = $chargeMeta['name'];
                                        $rateString = $formatRate($chargeRate);
                                    @endphp
                                    <div class="col-6 col-md-4 col-lg-3 gateway-container" data-name="{{ strtolower($gateway->name) }}">
                                        <label class="payment-gateway-item d-flex flex-column align-items-center justify-content-center p-3 rounded-3 text-center" for="{{ $gateway->name . '-' . $loop->index }}">
                                            <img class="img-fluid gateway-logo mb-2" src="{{ $gateway->image_url }}" alt="{{ $gateway->name }}" />
                                            <strong class="gateway-name">{{ $gateway->name }}</strong>
                                            <small class="gateway-currency text-muted">{{ $chargeSymbol }} {{ $chargeCode }}</small>
                                            <input
                                                type="radio"
                                                class="d-none"
                                                name="payment_gateway_id"
                                                id="{{ $gateway->name . '-' . $loop->index }}"
                                                value="{{ $gateway->id }}"
                                                data-code="{{ $chargeCode }}"
                                                data-rate="{{ $rateString }}"
                                                data-name="{{ $gateway->name }}"
                                                data-symbol="{{ $chargeSymbol }}"
                                                data-rate-display="{{ $rateString }}"
                                                @checked(old('payment_gateway_id') == $gateway->id || ($loop->first && !old('payment_gateway_id')))
                                            >
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            <div id="no-gateway-message" class="alert alert-warning mt-3 d-none">
                                @lang('No payment gateway found.')
                            </div>
                        </div>

                        <hr class="my-4">

                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h4 class="fw-semibold mb-0">@lang('Enter Amount')</h4>
                                <small class="text-muted">@lang('Entering amount in') <strong>{{ $baseCode }}</strong></small>
                            </div>

                            @php
                                $preAmounts = [100, 200, 300, 500, 1000, 2000];
                            @endphp
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                @foreach ($preAmounts as $amount)
                                    <button type="button" class="btn btn-outline-secondary rounded-pill pre-amount" data-amount="{{ $amount }}">{{ $baseSymbol }}{{ $amount }}</button>
                                @endforeach
                            </div>

                            <div class="input-group input-group-lg">
                                <span class="input-group-text">{{ $baseSymbol }}</span>
                                <input
                                    type="number"
                                    id="donation-amount"
                                    class="form-control"
                                    step="0.01"
                                    min="0"
                                    name="amount"
                                    value="{{ old('amount') }}"
                                    placeholder="@lang('Enter amount in :code', ['code' => $baseCode])"
                                    required
                                />
                            </div>
                            <small class="text-muted d-block mt-2">
                                @lang('The system currency is :code. Some gateways may bill in a different currency; we will automatically convert using the configured rates.', ['code' => $baseCode])
                            </small>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                @lang('Proceed to Payment')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="sticky-lg-top summary-wrapper">
                <div class="card shadow-sm border-0 rounded-4 mb-4" id="payment-summary-card">
                    <div class="card-body p-4">
                        <h5 class="fw-semibold mb-3">@lang('Payment Overview')</h5>

                        <div id="summary-empty" class="text-muted small py-3">
                            @lang('Select a payment gateway to see the live conversion and total amount that will be charged.')
                        </div>

                        <div id="summary-detail" class="d-none">
                            <div class="summary-row mb-3">
                                <div class="text-muted text-uppercase small fw-semibold">@lang('Base Currency')</div>
                                <div id="summary-base" class="fs-6 fw-semibold">{{ $baseSymbol }} {{ $baseCode }}</div>
                            </div>

                            <div class="summary-row mb-3">
                                <div class="text-muted text-uppercase small fw-semibold">@lang('Gateway')</div>
                                <div id="summary-gateway-name" class="fs-6 fw-semibold">—</div>
                                <div class="small text-muted" id="summary-gateway-currency"></div>
                            </div>

                            <div class="summary-row mb-3">
                                <div class="text-muted text-uppercase small fw-semibold">@lang('Conversion')</div>
                                <div id="summary-conversion" class="fw-semibold">—</div>
                                <div class="small text-muted" id="summary-note"></div>
                                <div class="small text-muted mt-1" id="summary-hint"></div>
                            </div>

                            <div class="summary-row">
                                <div class="text-muted text-uppercase small fw-semibold">@lang('To be Charged')</div>
                                <div id="summary-total" class="fs-4 fw-bold">—</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-semibold mb-3">@lang('Quick Tips')</h6>
                        <ul class="list-unstyled text-muted small mb-0 quick-tips">
                            <li>
                                <span class="bullet-dot"></span>
                                @lang('We calculate conversion using the gateway currency rates configured by your administrator.')
                            </li>
                            <li>
                                <span class="bullet-dot"></span>
                                @lang('Amounts may appear in another currency at the provider’s checkout – this is expected when their settlement currency differs.')
                            </li>
                            <li>
                                <span class="bullet-dot"></span>
                                @lang('Need to change exchange rates? Go to the gateway settings inside the admin panel.')
                            </li>
                        </ul>
                    </div>
                </div>

                @if ($chargeCurrencyList->isNotEmpty())
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body p-4">
                            <h6 class="fw-semibold mb-3">@lang('Currencies in Use')</h6>
                            <ul class="list-unstyled currency-tag-list d-flex flex-wrap gap-2 mb-0">
                                @foreach ($chargeCurrencyList as $item)
                                    <li class="badge text-bg-light currency-chip">
                                        {{ $item['symbol'] }} {{ $item['code'] }}
                                        <span class="text-muted">{{ $item['name'] }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
body {
    background-color: #f6f8fb;
}

.hero-section {
    max-width: 760px;
    margin: 0 auto;
}

.hero-badge {
    border-radius: 999px;
    padding: 0.35rem 0.85rem;
    font-weight: 600;
}

.hero-metrics .metric-dot {
    display: inline-block;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0d6efd, #6610f2);
    margin-right: 0.5rem;
}

.payment-workflow {
    border: 1px solid rgba(13, 110, 253, 0.06);
}

.stepper {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    border-left: 2px solid rgba(13, 110, 253, 0.15);
    padding-left: 1rem;
}

@media (min-width: 768px) {
    .stepper {
        flex-direction: row;
        gap: 2rem;
        border-left: none;
        padding-left: 0;
        justify-content: space-between;
    }
}

.step {
    display: flex;
    align-items: flex-start;
    gap: 0.9rem;
}

.step-index {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    font-weight: 700;
    background: rgba(13, 110, 253, 0.08);
    color: #0d6efd;
}

.step.active .step-index {
    background: linear-gradient(135deg, #0d6efd, #6610f2);
    color: #fff;
}

.search-box {
    min-width: 240px;
}

.payment-gateway-item {
    cursor: pointer;
    transition: all 0.2s ease-in-out;
    border: 1px solid #dee2e6;
    height: 150px;
    background-color: #fff;
}

.payment-gateway-item:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 25px rgba(13, 110, 253, 0.12);
    border-color: #0d6efd;
}

.gateway-logo {
    max-width: 80px;
    max-height: 42px;
    object-fit: contain;
}

.gateway-name {
    font-size: 0.95rem;
    margin-bottom: 0.1rem;
}

.gateway-currency {
    font-size: 0.75rem;
}

input[type="radio"]:checked + .payment-gateway-item {
    border-color: #0d6efd;
    background-color: rgba(13, 110, 253, 0.04);
    box-shadow: 0 4px 18px rgba(13, 110, 253, 0.2);
    transform: translateY(-4px);
}

.pre-amount {
    border-radius: 50px;
    font-weight: 500;
    transition: all 0.2s;
}

.pre-amount:hover,
.pre-amount.active {
    background: linear-gradient(135deg, #0d6efd, #6610f2);
    color: #fff;
    border-color: transparent;
}

.summary-wrapper {
    top: 20px;
}

.quick-tips .bullet-dot {
    display: inline-block;
    width: 7px;
    height: 7px;
    margin-right: 0.5rem;
    border-radius: 50%;
    background: #0d6efd;
}

.summary-row + .summary-row {
    border-top: 1px dashed rgba(13, 110, 253, 0.15);
    padding-top: 1rem;
}

.currency-tag-list .currency-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.78rem;
    padding: 0.45rem 0.65rem;
    border-radius: 999px;
    background: rgba(13, 110, 253, 0.08);
    color: #0d6efd;
    border: none;
}

.currency-tag-list .currency-chip span {
    color: rgba(33, 37, 41, 0.6);
}

@media (max-width: 991.98px) {
    .summary-wrapper {
        position: static;
    }
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    const baseCode = @json($baseCode);
    const baseSymbol = @json($baseSymbol);
    const currencyMetadata = @json($currencyMetadata->toArray());

    const summaryTexts = {
        chooseGateway: @json(__('Select a gateway to see the payment overview.')),
        enterAmount: @json(__('Enter an amount to preview the converted total.')),
        converted: @json(__('Amount converts using administrator-managed rates.')),
        sameCurrency: @json(__('This gateway charges directly in your base currency.')),
    };

    const amountFormatter = new Intl.NumberFormat(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const rateFormatter = new Intl.NumberFormat(undefined, { minimumFractionDigits: 0, maximumFractionDigits: 6 });

    function symbolFor(code) {
        return (currencyMetadata[code] && currencyMetadata[code].symbol) ? currencyMetadata[code].symbol : code;
    }

    function syncPredefinedButtons(value) {
        $('.pre-amount').removeClass('active');
        $('.pre-amount').each(function() {
            if ($(this).data('amount') == value) {
                $(this).addClass('active');
            }
        });
    }

    function formatBaseAmount(amount) {
        return `${baseSymbol}${amountFormatter.format(amount)} ${baseCode}`;
    }

    function formatGatewayAmount(amount, code, symbolOverride = null) {
        const symbol = symbolOverride || symbolFor(code);
        return `${symbol}${amountFormatter.format(amount)} ${code}`;
    }

    function formatRate(rate, code) {
        return `${rateFormatter.format(rate)} ${code}`;
    }

    function updateSummary() {
        const selected = $('input[name="payment_gateway_id"]:checked');
        const detail = $('#summary-detail');
        const empty = $('#summary-empty');

        if (!selected.length) {
            detail.addClass('d-none');
            empty.removeClass('d-none').text(summaryTexts.chooseGateway);
            return;
        }

        const rate = parseFloat(selected.data('rate')) || 1;
        const code = selected.data('code') || baseCode;
        const chargeMeta = currencyMetadata[code] || {};
        const chargeName = chargeMeta.name || code;
        const chargeSymbol = selected.data('symbol') || chargeMeta.symbol || symbolFor(code);
        const gatewayName = selected.data('name') || '—';
        const amountField = $('#donation-amount');
        const amountValue = parseFloat(amountField.val());
        const symbolBetween = rate === 1 ? '=' : '≈';

        $('#summary-gateway-name').text(gatewayName);
        $('#summary-gateway-currency').text(`${chargeName} (${code})`);
        $('#summary-conversion').text(`1 ${baseCode} ${symbolBetween} ${formatRate(rate, code)}`);
        $('#summary-note').text(rate === 1 ? summaryTexts.sameCurrency : summaryTexts.converted);

        if (!isNaN(amountValue) && amountValue > 0) {
            const convertedAmount = amountValue * rate;
            $('#summary-total').text(formatGatewayAmount(convertedAmount, code, chargeSymbol));
            $('#summary-hint').text(`${formatBaseAmount(amountValue)} ${symbolBetween} ${formatGatewayAmount(convertedAmount, code, chargeSymbol)}`);
        } else {
            $('#summary-total').text('—');
            $('#summary-hint').text(summaryTexts.enterAmount);
        }

        empty.addClass('d-none');
        detail.removeClass('d-none');
    }

    $('input[name="payment_gateway_id"]').on('change', function() {
        updateSummary();
    });

    $('.pre-amount').on('click', function() {
        const amount = $(this).data('amount');
        $('#donation-amount').val(amount);
        syncPredefinedButtons(amount);
        updateSummary();
    });

    $('#donation-amount').on('input', function() {
        const currentVal = $(this).val();
        syncPredefinedButtons(currentVal);
        updateSummary();
    });

    $('#gateway-search').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        let visibleGateways = 0;

        $('#payment-gateway-list .gateway-container').each(function() {
            const gatewayName = $(this).data('name');
            if (gatewayName.includes(searchTerm)) {
                $(this).show();
                visibleGateways++;
            } else {
                $(this).hide();
            }
        });

        if (visibleGateways === 0) {
            $('#no-gateway-message').removeClass('d-none');
        } else {
            $('#no-gateway-message').addClass('d-none');
        }
    });

    updateSummary();
    syncPredefinedButtons($('#donation-amount').val());
});
</script>
@endpush

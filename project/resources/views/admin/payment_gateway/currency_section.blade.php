<div class="col-12">
    <div class="border rounded-3 p-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
            <div>
                <h5 class="mb-1">@lang('Gateway Currencies')</h5>
                <p class="text-muted small mb-0">
                    @lang('Set how much each currency receives for 1 :base.', ['base' => $baseCurrency])
                </p>
            </div>
            <button type="button" class="btn btn-outline-theme btn-sm mt-3 mt-md-0" id="add-currency-row">
               <i class="fas fa-plus"></i>@lang('Add Currency')
            </button>
        </div>

        <div id="gateway-currency-rows" class="row gy-3">
            @foreach ($currencyRows as $index => $currency)
                @php
                    $selectedCode = $currency->currency_code ?? null;
                    $rateValue = old('currencies.' . $index . '.rate', $currency->rate ?? null);
                    $isDefault =
                        $selectedCode && ($defaultCurrency === $selectedCode || ($currency->is_default ?? false));
                @endphp
                <div class="gateway-currency-row row gx-3 align-items-end" data-index="{{ $index }}">
                    <div class="col-md-4">
                        <label class="form-label">@lang('Currency')</label>
                        <select class="js-select2 form-control" name="currencies[{{ $index }}][code]"
                            class="form-select gateway-currency-select">
                            <option value="">@lang('Select currency')</option>
                            @if ($selectedCode && !in_array($selectedCode, $supportedCurrencies))
                                <option value="{{ $selectedCode }}" selected>
                                    {{ $selectedCode }} @lang('(not supported)')
                                </option>
                            @endif
                            @foreach ($supportedCurrencies as $code)
                                <option value="{{ $code }}" @selected($selectedCode === $code)>
                                    {{ $code }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">@lang('Rate (1 :base = ?)', ['base' => $baseCurrency])</label>
                        <input type="number" step="0.00000001" min="0.00000001"
                            name="currencies[{{ $index }}][rate]" class="form-control"
                            value="{{ $rateValue }}" placeholder="1.00" />
                    </div>

                    <div class="col-md-3">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="radio" name="default_currency"
                                value="{{ $selectedCode }}" id="default_currency_{{ $index }}"
                                @checked($isDefault)>
                            <label class="form-check-label" for="default_currency_{{ $index }}">
                                @lang('Use as default')
                            </label>
                        </div>
                    </div>

                    <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger remove-currency-row mt-4">
                          <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <template id="gateway-currency-row-template">
            <div class="gateway-currency-row row gx-3 align-items-end" data-index="__INDEX__">
                <div class="col-md-4">
                    <label class="form-label">@lang('Currency')</label>
                    <select name="currencies[__INDEX__][code]" class="js-select2 form-control gateway-currency-select">
                        <option value="">@lang('Select currency')</option>
                        @foreach ($supportedCurrencies as $code)
                            <option value="{{ $code }}">{{ $code }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">@lang('Rate (1 :base = ?)', ['base' => $baseCurrency])</label>
                    <input type="number" step="0.00000001" min="0.00000001" name="currencies[__INDEX__][rate]"
                        class="form-control" placeholder="1.00" />
                </div>

                <div class="col-md-3">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="radio" name="default_currency" value=""
                            id="default_currency___INDEX__">
                        
                        <label class="form-check-label" for="default_currency___INDEX__">
                            @lang('Use as default')
                        </label>
                    </div>
                </div>

                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-danger remove-currency-row mt-4">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </template>

        @error('currencies')
            <div class="alert alert-danger mt-3 mb-0">{{ $message }}</div>
        @enderror

        @error('default_currency')
            <div class="alert alert-danger mt-3 mb-0">{{ $message }}</div>
        @enderror
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let currencyIndex = {{ $currencyRows->count() }};
            const rowsContainer = document.getElementById('gateway-currency-rows');
            const template = document.getElementById('gateway-currency-row-template');

            // Add new currency row
            document.getElementById('add-currency-row')?.addEventListener('click', function() {
                if (!template || !rowsContainer) return;

                let html = template.innerHTML.replace(/__INDEX__/g, currencyIndex);
                const wrapper = document.createElement('div');
                wrapper.innerHTML = html.trim();

                const row = wrapper.firstElementChild;
                if (!row) return;

                rowsContainer.appendChild(row);

                currencyIndex++;
                  $(".js-select2").select2();
            });

            // Remove currency row
            rowsContainer?.addEventListener('click', function(event) {
                const trigger = event.target.closest('.remove-currency-row');
                if (!trigger) return;

                const row = trigger.closest('.gateway-currency-row');
                if (row) row.remove();
            });

            // Update radio when currency changes
            rowsContainer?.addEventListener('change', function(event) {
                if (!event.target.classList.contains('gateway-currency-select')) return;

                const row = event.target.closest('.gateway-currency-row');
                if (!row) return;

                const radio = row.querySelector('input[type="radio"][name="default_currency"]');
                if (radio) radio.value = event.target.value;
            });
        });
    </script>
@endpush

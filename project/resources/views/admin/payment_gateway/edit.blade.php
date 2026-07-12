@extends('admin.layouts.master')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-header__title">@lang('Payment Gateway Configuration')</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.payment_gateway.save', $paymentGateway->key) }}" method="POST"
                  enctype="multipart/form-data">
                @csrf

                <div class="row gy-4">
                    <div class="col-lg-4">
                        <x-uploade-image  name="image" :path="get_img(filePath('paymentGateway') . '/' . $paymentGateway->image)"/>
                    </div>

                    <div class="col-lg-12">
                        <x-form.group for="short_desc" class="form-group" label="Short Desc">
                            <textarea name="short_desc" id="short_desc" class="form-control">{{ $paymentGateway->short_desc }}</textarea>
                        </x-form.group>
                    </div>

                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="form-label">@lang('Name')</label>
                            <input type="text" name="name" class="form-control"
                                   value="{{ old('name', $paymentGateway->name) }}"
                                   placeholder="@lang('Enter name')">
                        </div>
                    </div>

                    {{-- Dynamic Gateway Config Inputs --}}
                    @foreach ($gateway->filledConfig() as $inputField)
                        <div class="col-lg-4">
                            {!! $inputField !!}
                        </div>
                    @endforeach

                    {{-- Currency Section --}}
                    @if (!empty($supportedCurrencies))
                        @include('admin.payment_gateway.currency_section', [
                            'currencyRows' => $currencyRows,
                            'supportedCurrencies' => $supportedCurrencies,
                            'defaultCurrency' => $defaultCurrency,
                            'baseCurrency' => $baseCurrency,
                        ])
                    @endif

                    <div class="col-lg-4">
                        <x-form.group for="is_test_mode" label="Test Mode">
                            <select name="is_test_mode" id="is_test_mode" data-search="false" class="js-select2 form-control">
                                <option value="1" @selected($paymentGateway->is_test_mode == 1)>@lang('Yes')</option>
                                <option value="0" @selected($paymentGateway->is_test_mode == 0)>@lang('No')</option>
                            </select>
                        </x-form.group>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-outline-theme">
                       <i class="fas fa-save"></i>@lang('Submit')
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('breadcrumb')
<x-back link="{{ route('admin.payment_gateway.list') }}" />
@endpush

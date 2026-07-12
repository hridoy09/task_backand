@extends('admin.layouts.master')

@section('content')
    <div class="row gy-3 double-column-row">
        <div class="col-md-12">
            <div class="payment-gateway-table-wrapper table-responsive">
                <table class="table payment-gateway-table">
                    <thead>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Mode')</th>
                            <th>@lang('Action')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($paymentGateways->where('manual', 0) as $gateway)
                            <tr>
                                <td table-label="Name">
                                    <div class="payment-gateway-author">
                                        <span class="payment-gateway-author__thumb">
                                            <img src="{{ get_img(filePath('paymentGateway') . '/' . $gateway->image) }}"
                                                alt="" class="image-fitted">
                                        </span>
                                        <h6 class="payment-gateway-author__name">{{ __($gateway->name) }}</h6>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        if ($gateway->is_test_mode) {
                                            echo getBadge('Test Mode', 'danger');
                                        } else {
                                            echo getBadge('Live', 'success');
                                        }
                                    @endphp

                                </td>
                                <td table-label="Action">
                                    <div class="payment-gateway-action">
                                        <div class="form-switch">
                                            <input class="form-check-input" type="checkbox" data-action="{{ route('admin.payment_gateway.status.change', $gateway->key) }}" role="switch"
                                                id="paymentGatewaySwitch" @checked($gateway->status)>
                                        </div>
                                        <a href="{{ route('admin.payment_gateway.edit', $gateway->key) }}"
                                            class="btn btn-outline-theme">
                                            <span class="svg-icon">
                                                <i class="fas fa-cog"></i>
                                            </span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%">
                                    <x-admin-no-data />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>


  
    </div>
@endsection

@push('scripts')
    <script>
  
        $(document).on('change', '#paymentGatewaySwitch', function() {
            console.log(11);
            
            const url = $(this).data('action');
            console.log(url);
            
            const status = $(this).prop('checked');
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    status: status,
                },
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                success: function(response) {
                    
                    if (response.status == 'success') {
                        toastr.success(response.message);
                    } else {
                        toastr.error(response.message);
                    }
                }
            });
        });
    </script>
@endpush

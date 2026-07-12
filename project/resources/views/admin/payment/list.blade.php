@extends('admin.layouts.master')

@section('content')
    <div class="table-responsive">
        <table class="table responsive-table-sm manage-page-table">
            <thead class="table-light">
                <tr>
                    <th>@lang('Txn #')</th>
                    <th>@lang('User')</th>
                    <th>@lang('Gateway')</th>
                    <th>@lang('Amount')</th>
                    <th>@lang('Currency')</th>
                    <th>@lang('Status')</th>
                    <th>@lang('Paid At')</th>
                    <th>@lang('Created')</th>
                    <th>@lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $payment)
                    @php
                        $user = $payment->user;
                        $gateway = $payment->paymentGateway;
                    @endphp
                    <tr class="{{ $payment->status === 'success' ? '' : 'table-row-soft' }}">
                        <td>
                            <span class="fw-semibold">{{ $payment->transaction_no ?? '—' }}</span> <br>
                        </td>

                        <td>
                            @if ($user)
                                <div>
                                    <a href="{{ route('admin.user.details', $user->id) }}"
                                        class="fw-semibold text-decoration-none">
                                        {{ $user->name ?? ($user->username ?? 'User #' . $user->id) }}
                                    </a> <br>
                                    <small class="text-muted">
                                        {{ $user->email ?? '—' }}
                                    </small>
                                </div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        <td>
                            <div>
                                <img src="{{ $gateway->image_url }}" alt="{{ $gateway->name }}" class="rounded"
                                    style="width:22px;height:22px;object-fit:contain;">

                                <span class="fw-semibold">{{ $gateway->name ?? strtoupper($payment->method ?? '') }}</span>
                                <br>
                                <small class="text-muted">{{ $gateway->key ?? '' }}</small>
                            </div>
                        </td>

                        <td>
                            {{ System::amountWithCurrency($payment->amount) }}
                        </td>

                        <td>{{ $payment->currency ?? '—' }}</td>

                        <td>{!! $payment->statusBadge !!}</td>

                        <td>
                            @if ($payment->paid_at)
                                <div>
                                    <span>{{ \Illuminate\Support\Carbon::parse($payment->paid_at)->format('Y-m-d H:i') }}</span>
                                    <br>
                                    <small
                                        class="text-muted">{{ \Illuminate\Support\Carbon::parse($payment->paid_at)->diffForHumans() }}</small>
                                </div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        <td>
                            <div>
                                <span>{{ $payment->created_at?->format('Y-m-d H:i') }}</span> <br>
                                <small class="text-muted">{{ $payment->created_at?->diffForHumans() }}</small>
                            </div>
                        </td>

                        <td>

                            <button type="button" class="btn btn-outline-theme" data-bs-toggle="modal"
                                data-bs-target="#paymentMetaModal" data-meta='@json($payment->meta)'
                                data-title="{{ $payment->transaction_no ?? '#' . $payment->id }}">
                                <i class="fas fa-desktop"></i>
                            </button>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="100%" class="text-center py-4 text-muted">
                            <x-admin-no-data />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <x-paginate :table="$payments" />
    </div>

    {{-- Meta viewer modal --}}
    <div class="modal fade" id="paymentMetaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Payment Meta') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <strong class="me-2">{{ __('Transaction') }}:</strong>
                        <span id="metaTxnTitle">—</span>
                    </div>
                    <pre id="metaJson" class="bg-light p-3 rounded small mb-0" style="max-height: 50vh; overflow:auto;">{}</pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal"><i
                            class="fas fa-times"></i>{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb')
    <form method="GET" class="d-flex flex-wrap gap-2">
        <div class="w-auto">
            <select name="status" data-seacrh="false" class="form-control js-select2 ">
                @php $current = request('status'); @endphp
                <option value="">{{ __('All statuses') }}</option>
                <option value="pending" {{ $current === 'pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                <option value="success" {{ $current === 'success' ? 'selected' : '' }}>{{ __('Success') }}</option>
                <option value="failed" {{ $current === 'failed' ? 'selected' : '' }}>{{ __('Failed') }}</option>
                <option value="refunded" {{ $current === 'refunded' ? 'selected' : '' }}>{{ __('Refunded') }}</option>
            </select>
        </div>

        <input type="text" name="search" value="{{ request('search') }}" class="form-control w-auto"
            placeholder="{{ __('Search txn/user/email…') }}" />

        <button class="btn  btn-outline-theme w-auto"><i class="fas fa-filter"></i>{{ __('Filter') }}</button>
        @if (request()->hasAny(['status', 'q']))
            <a href="{{ request()->url() }}" class="btn btn-outline-secondary">{{ __('Reset') }}</a>
        @endif
    </form>

@endpush


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modal = document.getElementById('paymentMetaModal');
            if (!modal) return;

            modal.addEventListener('show.bs.modal', function(event) {
                var btn = event.relatedTarget;
                var meta = btn?.getAttribute('data-meta') || '{}';
                var title = btn?.getAttribute('data-title') || '—';

                // Pretty print JSON safely
                try {
                    var parsed = JSON.parse(meta);
                    meta = JSON.stringify(parsed, null, 2);
                } catch (_) {}

                modal.querySelector('#metaJson').textContent = meta;
                modal.querySelector('#metaTxnTitle').textContent = title;
            });
        });
    </script>
@endpush

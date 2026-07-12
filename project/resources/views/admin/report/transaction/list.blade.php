@extends('admin.layouts.master')

@section('content')
    <div class="table-responsive">
        <table class="table responsive-table-sm manage-page-table">
            <thead class="table-light">
                <tr>
                    <th data-label="Trx #">@lang('Trx #')</th>
                    <th>@lang('User')</th>
                    <th>@lang('Type')</th>
                    <th>@lang('Amount')</th>
                    <th>@lang('Balance Before')</th>
                    <th>@lang('Balance After')</th>
                    <th>@lang('Category')</th>
                    <th>@lang('Remark')</th>
                    <th>@lang('Created')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $trx)
                    @php $user = $trx->user; @endphp
                    <tr>
                        <td>
                            <span class="fw-semibold">{{ $trx->trx ?? '—' }}</span>
                        </td>

                        <td>
                            @if ($user)
                                <div>
                                    <a href="{{ route('admin.user.details', $user->id) }}"
                                        class="fw-semibold text-decoration-none">
                                        {{ $user->name ?? ($user->username ?? 'User #' . $user->id) }}
                                    </a><br>
                                    <small class="text-muted">{{ $user->email ?? '—' }}</small>
                                </div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        <td>
                            <span class="badge badge-{{ $trx->trx_type === 'credit' ? 'success' : 'danger' }}">
                                {{ ucfirst($trx->trx_type ?? '—') }}
                            </span>
                        </td>

                        <td>
                            {{ System::amountWithCurrency($trx->amount) }}
                        </td>


                        <td>{{ System::amountWithCurrency($trx->balance_before) }}</td>
                        <td>{{ System::amountWithCurrency($trx->balance_after) }}</td>

                        <td>{{ ucfirst($trx->category ?? '—') }}</td>
                        <td>{{ $trx->remark ?? '—' }}</td>

                        <td>

                            <span>{{ $trx->created_at?->format('Y-m-d H:i') }}</span> <br>
                            <small class="text-muted">{{ $trx->created_at?->diffForHumans() }}</small>

                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="100%" class="text-center  text-muted m-0">
                            <x-admin-no-data />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <x-paginate :table="$transactions" />
    </div>
    {{-- Meta viewer modal --}}
    <div class="modal fade" id="trxMetaModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Transaction Meta') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="{{ __('Close') }}"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <strong class="me-2">{{ __('Transaction') }}:</strong>
                        <span id="trxMetaTitle">—</span>
                    </div>
                    <pre id="trxMetaJson" class="bg-light p-3 rounded small mb-0" style="max-height: 50vh; overflow:auto;">{}</pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary btn-sm"
                        data-bs-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb')
    <form method="GET" class="d-flex flex-wrap gap-2">
        <div class="w-auto">
            <select data-search="false" class="js-select2" name="category">
                @php $current = request('category'); @endphp
                <option value="" {{ blank($current) ? 'selected' : '' }}>{{ __('All Categories') }}</option>
                <option value="deposit" {{ $current == 'deposit' ? 'selected' : '' }}>{{ __('Deposit') }}</option>
                <option value="withdraw" {{ $current == 'withdraw' ? 'selected' : '' }}>{{ __('Withdraw') }}</option>
                <option value="transfer" {{ $current == 'transfer' ? 'selected' : '' }}>{{ __('Transfer') }}</option>
            </select>
        </div>

        <input type="text" name="search" value="{{ request('search') }}" class="form-control w-auto"
            placeholder="{{ __('Search trx/user/email…') }}" />

        <button class="btn btn-outline-theme w-auto"><i class="fas fa-filter"></i>{{ __('Filter') }}</button>

        @if (request()->hasAny(['category', 'search']))
            <a href="{{ request()->url() }}" class="btn btn-outline-secondary w-auto">
                <i class="fas fa-undo"></i>{{ __('Reset') }}
            </a>
        @endif
    </form>
@endpush

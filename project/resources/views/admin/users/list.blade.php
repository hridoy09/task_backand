@extends('admin.layouts.master')

@section('content')
    <div class="table-wrapper table-responsive">
        <table class="table theme-tab-listle responsive-table-sm">
            <thead>
                <tr>
                    <th>@lang('Name')</th>
                    <th>@lang('Username')</th>
                    <th>@lang('Email')</th>
                    <th>@lang('Email Verified')</th>
                    <th>@lang('Profile Completed')</th>
                    <th>@lang('Phone Number')</th>
                    <th>@lang('Last Login')</th>
                    <th class="text-end">@lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $user)
                    <tr>
                        <td data-label="@lang('Name')">
                            <div class="d-flex flex-column">
                                <a href="{{ route('admin.user.details', $user->id) }}"
                                    class="fw-semibold text-decoration-none">
                                    {{ $user->name ?? ($user->username ?? 'User #' . $user->id) }}
                                </a>
                                <small class="text-muted">
                                    {{ $user->email ?? '—' }}
                                </small>
                            </div>
                        </td>
                        <td>{{ $user->username ?? '-' }}</td>
                        <td>{{ $user->email ?? '-' }}</td>
                        <td>
                            @php echo $user->emailVerifiedBadge; @endphp
                        </td>
                        <td>
                            @php echo $user->profileCompletedBadge; @endphp
                        </td>
                        <td>{{ $user->phone_number ?? '-' }}</td>
                        <td>
                            {{ $user->userLogins()->latest()->first()->created_at ?? '-' }}
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-3 justify-content-end">
                                @if ($user->has_pending_kyc)
                                    <a class="btn btn-theme" title="@lang('View KYC Data')" href="{{ route('admin.user.kyc.data', $user->id) }}">
                                        <i class="fas fa-user-check"></i>
                                        @lang('KYC Details')
                                    </a>
                                @endif

                                <a class="btn btn-outline-theme"  href="{{ route('admin.user.details', $user->id) }}">
                                    <i class="fas fa-desktop"></i>
                                  
                                </a>
                                <button class="btn btn-outline-danger confirmBtn"
                                    data-action="{{ route('admin.user.delete', $user->id) }}" data-question="@lang('Are you sure you want to delete this user?')" >
                                    <i class="fas fa-trash"></i>
                                  
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="100%" class="text-center">
                            <x-admin-no-data />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <x-paginate :table="$data" />
    </div>

 
@endsection
@push('breadcrumb')
    <x-form.search />
@endpush

@push('styles')
    <style>
        .table-card {
            border: none;
            border-radius: 4px;
        }

        .table-image-cell {
            display: flex;
            align-items: center;
            justify-content: start;
            gap: 10px;
            --square: 40px;
        }

        .table-image-cell img {
            width: var(--square);
            height: var(--square);
            border-radius: 50%;
        }
    </style>
@endpush

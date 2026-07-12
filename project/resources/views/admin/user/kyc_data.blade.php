@extends('admin.layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <h4 class="mb-0">{{ $title }}</h4>

        <div class="d-flex gap-2">
            <x-button href="{{ route('admin.user.login', $user->id) }}" class="me-2" var="second">
                <x-icons.login />
                <span class="ms-2">@lang('Login as User')</span>
            </x-button>
            <x-button href="{{ route('admin.user.list') }}">
                <x-icons.back-v1 /> 
                <span class="ms-1">@lang('Back')</span>
            </x-button>
        </div>
    </div>

    {{-- Session Messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $e)
                <div>{{ $e }}</div>
            @endforeach
        </div>
    @endif

    {{-- Handle Case Where No KYC Submission Exists --}}
    @if(!$kycData)
        <div class="alert alert-secondary mb-3">
            @lang('No KYC submission found for this user.')
        </div>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title mb-2">
                    {{ $user->name }}
                    <span class="text-muted">(#{{ $user->id }})</span>
                </h5>
                <div class="text-muted small mb-0">
                    <strong>@lang('Email'):</strong> {{ $user->email }}
                </div>
            </div>
        </div>
    @else
        {{-- Display KYC Submission Details --}}
        <div class="card mb-4">
            <div class="card-body">
                {{-- User and Status Header --}}
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h5 class="card-title mb-1">
                            {{ $user->name }}
                            <span class="text-muted">(#{{ $user->id }})</span>
                        </h5>
                        <div class="text-muted small">{{ $user->email }}</div>
                    </div>

                    <span class="badge
                        @if($kycData->status === 'pending') bg-info
                        @elseif($kycData->status === 'approved') bg-success
                        @else bg-danger @endif
                        px-3 py-2 text-uppercase">
                        {{ $kycData->status }}
                    </span>
                </div>

                <hr class="my-3">

                <div class="row g-4">
                    {{-- Submitted Data Section --}}
                    {{-- Adjust column width based on the presence of review_note --}}
                    <div class="{{ !empty($kycData->review_note) ? 'col-lg-6' : 'col-lg-12' }}">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">@lang('Submitted Information')</h5>
                            </div>
                            <div class="card-body p-0">
                                @if(!empty($kycData->submitted_data))
                                    <table class="table table-sm table-bordered mb-0">
                                        <tbody>
                                            @foreach($kycData->submitted_data as $key => $value)
                                                <tr>
                                                    {{-- Format the label for readability --}}
                                                    <th class="text-capitalize w-40">{{ str_replace('_', ' ', $key) }}</th>
                                                    <td>
                                                        @if(is_array($value))
                                                            {{ implode(', ', $value) }}
                                                        @else
                                                            {{ $value ?? 'N/A' }}
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="p-3 text-muted">@lang('No submitted data found.')</div>
                                @endif
                            </div>
                             <div class="card-footer text-muted small">
                                @lang('Submitted at'):
                                {{ $kycData->created_at?->format('Y-m-d H:i') }}
                            </div>
                        </div>
                    </div>
                    
                    {{-- Reviewer Note (if exists) --}}
                    @if(!empty($kycData->review_note))
                        <div class="col-lg-6">
                             <div class="alert alert-warning h-100 mb-0">
                                <strong>@lang('Reviewer Note'):</strong>
                                <p class="mb-0 mt-1">{{ $kycData->review_note }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <hr class="my-3">

                @if($kycData->status === 'pending')
                    <div class="d-flex justify-content-end flex-wrap gap-2">
                        <x-button question="{{ __('Are you sure to approve this KYC Data?') }}" confirm href="{{ route('admin.user.kyc.approve', $user->id) }}" class="btn-success">
                            <x-icons.check-check />
                            @lang('Approve')
                        </x-button>

                        <x-button type="button" class="btn-danger" data-bs-toggle="collapse" data-bs-target="#rejectBox" aria-expanded="false">
                            <x-icons.shield-minus />
                            @lang('Reject')
                        </x-button>
                    </div>

                    {{-- Rejection Reason Form --}}
                    <div class="collapse mt-3" id="rejectBox">
                        <div class="card card-body border-danger">
                            <form action="{{ route('admin.user.kyc.reject', $user->id) }}" method="POST">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label">@lang('Reason for Rejection')</label>
                                    <textarea name="reason" class="form-control" rows="3"
                                              placeholder="@lang('Explain why it is rejected')" required></textarea>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-danger">
                                        @lang('Confirm Reject')
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    {{-- Display Final Status for Approved/Rejected KYC --}}
                    <div class="alert @if($kycData->status === 'approved') alert-success @else alert-danger @endif mb-0">
                        @if($kycData->status === 'approved')
                            <i class="bi bi-check-circle me-1"></i>@lang('This KYC is already approved.')
                        @else
                            <i class="bi bi-exclamation-octagon me-1"></i>@lang('This KYC was rejected.')
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection
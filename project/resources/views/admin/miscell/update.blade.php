@extends('admin.layouts.master')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                @if ($updateAvailable)
                    <div class="card update-card">
                        <div class="card-body text-center">
                            <div class="update-icon update-available">
                                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-arrow-up-circle">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="16 12 12 8 8 12"></polyline>
                                    <line x1="12" y1="16" x2="12" y2="8"></line>
                                </svg>
                            </div>
                            <h3 class="card-title">@lang('New Update Available!')</h3>
                            <p class="card-text text-muted">@lang('Version') {{ $newVersion }} @lang('is now available. Update to get the latest features and improvements').</p>
                            <button data-action="{{ route('admin.miscell.update.system') }}"
                                data-question="{{ __('Are you sure to update the system?') }}" type="submit"
                                class="btn btn-outline-theme confirmBtn">
                                <i class="fas fa-sync"></i>@lang('Update Now')
                            </button>
                        </div>
                    </div>
                @else
                    <div class="card update-card">
                        <div class="card-body text-center">
                            <div class="update-icon up-to-date">
                                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="feather feather-check-circle">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                </svg>
                            </div>
                            <h3 class="card-title">@lang('You are up to date!')</h3>
                            <p class="card-text text-muted">@lang('Your software is running the latest version') : <strong>{{ $currentVersion }}</strong>
                            </p>
                            <p class="card-text"><small class="text-muted">Last checked:
                                    {{ now()->toDateTimeString() }}</small></p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <x-confirm-modal />
@endsection


@push('styles')
    <style>
        .update-card {
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
            border: none;
            padding: 2rem;
        }

        .update-icon {
            margin-bottom: 1.5rem;
        }

        .update-icon.up-to-date svg {
            color: #28a745;
        }

        .update-icon.update-available svg {
            color: #007bff;
        }

        .card-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .btn-update {
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
        }
    </style>
@endpush

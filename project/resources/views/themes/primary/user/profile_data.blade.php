@extends('theme::layouts.main')

@section('content')
    <div class="container card my-5 p-4 shadow-sm">
        <h3 class="text-center fw-bold">@lang('Complete Your Profile')</h3>
        <p class="mb-4 text-center text-muted">@lang('Please complete your profile by submitting this form.')</p>

        <form method="POST" action="{{ route('user.save_profile_data') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label for="username" class="form-label">@lang('Username')</label>
                <input type="text" name="username" id="username" class="form-control"
                    value="{{ old('username', auth()->user()->username) }}" required>
            </div>

            <div class="mb-3">
                <label for="first_name" class="form-label">@lang('First Name')</label>
                <input type="text" name="first_name" id="first_name" class="form-control"
                    value="{{ old('first_name', auth()->user()->first_name) }}" required>
            </div>

            <div class="mb-3">
                <label for="last_name" class="form-label">@lang('Last Name')</label>
                <input type="text" name="last_name" id="last_name" class="form-control"
                    value="{{ old('last_name', auth()->user()->last_name) }}" required>
            </div>

            <div class="mb-3">
                <label for="phone_number" class="form-label">@lang('Phone Number')</label>
                <input type="text" name="phone_number" id="phone_number" class="form-control"
                    value="{{ old('phone_number', auth()->user()->phone_number) }}" required>
            </div>

            <div class="mb-4">
                <label for="country" class="form-label">@lang('Country Code')</label>
                <input type="text" name="country" id="country" class="form-control text-uppercase" maxlength="2"
                    value="{{ old('country', auth()->user()->country) }}" required>
                <div class="form-text">@lang('Use 2-letter country code, e.g., BD, US, IN')</div>
            </div>

            <button type="submit" class="btn btn-primary w-100 btn-lg">@lang('Submit')</button>
        </form>
    </div>
@endsection

@extends('theme::layouts.main')

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow rounded-3">
                    <div class="card-body p-4">
                        <h4 class="card-title text-center mb-3">@lang('Create an Account')</h4>
                        <p class="text-muted text-center mb-4">@lang('Fill in the form to register')</p>

                        <form method="POST" action="{{ route('register.store') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="first_name" class="form-label">@lang('First Name')</label>
                                <input type="text" id="first_name" name="first_name" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="last_name" class="form-label">@lang('Last Name')</label>
                                <input type="text" id="last_name" name="last_name" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">@lang('Email Address / Phone Number')</label>
                                <input type="text" id="email" name="email" class="form-control" required>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label">@lang('Password')</label>
                                <input type="password" name="password" class="form-control" id="password" required>
                            </div>

                            @if(System::googleCaptchaEnabled())
                                <x-recaptcha/>
                            @endif

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary">@lang('Sign Up')</button>
                            </div>

                            <div class="text-center">
                                <p class="mb-0 text-muted">
                                    @lang('Already have an account?')
                                    <a href="{{ route('login') }}" class="text-decoration-none text-primary">@lang('Login Now')</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@extends('theme::layouts.main')

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card shadow rounded-3">
                    <div class="card-body p-4">
                        <h4 class="card-title text-center mb-3">@lang('Forgot Password')</h4>
                        <p class="text-muted text-center mb-4">@lang('Enter your email or phone to reset your password')</p>

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">@lang('Phone Number / Email Address')</label>
                                <input type="text" name="email" class="form-control" id="email" required>
                            </div>
                            
                            @if(System::googleCaptchaEnabled())
                                <x-recaptcha/>
                            @endif

                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary">@lang('Send Reset Link')</button>
                            </div>

                            <div class="text-center">
                                <p class="mb-0 text-muted">
                                    @lang('Remember your password?')
                                    <a href="{{ route('login') }}" class="text-decoration-none text-primary">@lang('Login here')</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('admin.layouts.app')

@section('app')
    <section class="auth-section bg-image" data-bg-image={{asset("assets/admin/thumbs/login-bg.png")}}>
        <div class="auth-section__inner">
            <div class="auth-form">
                <h1 class="auth-form__title">Welcome Back!</h1>
                <p class="auth-form__desc">Login your account to get your access.</p>
                <form method="POST" action="{{ route('admin.login') }}">
                    @csrf
                    <x-form.group label="Username" for="username">
                        <input type="text" name="username" class="form-control" id="username"
                            placeholder="@lang('Please enter your username')" required autofocus />
                    </x-form.group>

                    <x-form.group label="Password" for="password">
                        <input type="password" name="password" class="form-control" id="password"
                            placeholder="@lang('Please enter your password')" required />
                    </x-form.group>

                    @if (System::googleCaptchaEnabled())
                        <x-recaptcha />
                    @endif

                    <div class="form-group">
                        <div class="d-flex flex-wrap align-items-center justify-content-between">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="remember" id="remember">
                                <label class="form-check-label" for="remember">@lang('Remember Me')</label>
                            </div>
                            <a href="{{ route('admin.password.forgot') }}" class="forgot-pass">@lang('Forget Password')?</a>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <div class="d-flex flex-wrap align-items-center justify-content-end">
                            <button type="submit" class="btn btn-theme">
                                <span class="text">Login</span>
                                <span class="icon"><i class="fa-solid fa-arrow-right"></i></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

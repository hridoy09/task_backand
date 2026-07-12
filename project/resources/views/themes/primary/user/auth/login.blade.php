@extends('theme::layouts.main')

@php
    $socialLogin = app(\App\Services\SocialLogin::class);
@endphp

@section('content')
    <div class="container-fluid my-5">
        <div class="row">
            <div class="col-md-4 offset-md-4">
                <x-card>
                    <div class="row g-2 justify-content-center mb-3">
                        @foreach($socialLogin->enabled() as $key => $provider)
                            <div class="col-md-6">
                                <a href="{{ route('social.redirect', $key) }}" class="btn w-100 border btn-social btn-{{ $key }}">
                                    <img src="{{ asset('assets/' . $provider['image']) }}" alt="@lang('Image')" width="20" />
                                    Login with {{ $provider['name'] }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                    
                    <form method="POST" action="{{ route('login.store') }}">
                        @csrf

                        <div class="col-sm-12 form-group mb-2">
                            <label for="username" class="form-label">@lang('Username / Email')</label>
                            <input type="text" id="username" name="username" class="form-control" required />
                        </div>

                        <div class="form-group mb-2">
                            <label for="password" class="form-label">@lang('Password')</label>
                            <input type="password" name="password" class="form-control" id="password" required />
                        </div>

                        <div class="col-sm-12 form-group mb-2">
                            <div class="d-flex my-3 flex-wrap justify-content-between">
                                <div class="form--check">
                                    <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">@lang('Remember me')</label>
                                </div>
                                <a href="{{ route('password.request') }}" class="forgot-password text--base">@lang('Forgot Your Password?')</a>
                            </div>
                        </div>

                        @if(System::googleCaptchaEnabled())
                            <x-recaptcha/>
                        @endif

                        <div class="col-sm-12 form-group mb-2">
                            <button type="submit" class="btn btn-primary w-100">@lang('Sign In')</button>
                        </div>

                        <div class="col-sm-12">
                            <div class="have-account text-center">
                                <p class="have-account__text">@lang("Don't Have An Account?")
                                    <a href="{{ route('register') }}"
                                        class="have-account__link text--base">@lang('Register Here')</a>
                                </p>
                            </div>
                        </div>
                    </form>
                </x-card>
            </div>
        </div>
    </div>
@endsection

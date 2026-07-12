<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Events\AdminLoggedIn;
use App\Http\Controllers\Controller;
use App\Models\AdminLogin;
use App\Notifications\AdminLoginAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Browser;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

class LoginController extends Controller
{
    public function showForgotPasswordForm()
    {
        $title = 'Reset Password';

        return view('admin.auth.forgot-password', compact('title'));
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:admins,email',
        ]);

        googleCaptchaVerify($request); 

        $status = Password::broker('admins')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function setPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email|exists:admins,email',
            'password' => 'required|confirmed',
        ]);

        googleCaptchaVerify($request);

        $status = Password::broker('admins')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($admin, $password) {
                $admin->password = Hash::make($password);
                $admin->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('admin.login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }


    public function showSetPasswordForm(Request $request)
    {
        $title = __('Reset Your Password');
        return view('admin.auth.set-password', ['token' => $request->token, 'email' => $request->email, 'title' => $title]);
    }

    public function showLoginForm()
    {
        $title = __('Admin Login');

        return view('admin.auth.login', compact('title'));
    }

    public function login(Request $request)
    {
        $this->ensureIsNotRateLimited($request);

        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        googleCaptchaVerify($request); 

        $isEmail = filter_var($request->username, FILTER_VALIDATE_EMAIL);
        
        if (Auth::guard('admin')->attempt(
            [($isEmail ? 'email' : 'username') => $credentials['username'], 'password' => $credentials['password']],
            $request->boolean('remember')
        )) {

            RateLimiter::clear($this->throttleKey($request));

            $agent   = \Browser::parse($request->userAgent() ?? '');
            $device  = $agent->deviceType();
            $browser = $agent->browserName();
            $os      = $agent->platformName();
            $sessionId = $request->session()->getId() ?? (string) Str::uuid();
            event(new AdminLoggedIn(
                admin: admin(),
                username: $credentials['username'],
                ip: $request->ip(),
                device: $device,
                browser: $browser,
                os: $os,
                sessionId: $sessionId,
            ));

            return redirect()->intended(route('admin.dashboard'));
        }

        RateLimiter::hit($this->throttleKey($request), 60);

        throw \Illuminate\Validation\ValidationException::withMessages([
            'username' => __('auth.failed'),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    protected function throttleKey(Request $request): string
    {
        return Str::lower($request->input('username')) . '|' . $request->ip();
    }

    protected function ensureIsNotRateLimited(Request $request): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        throw ValidationException::withMessages([
            'username' => __('Too many login attempts. Please try again in :seconds seconds.', [
                'seconds' => RateLimiter::availableIn($this->throttleKey($request)),
            ]),
        ]);
    }
}

<?php

namespace App\Http\Controllers\User\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class ResetPasswordController
{
    public function resetForm(Request $request) {
        $title = __('Set New Password');
        return theme('user.auth.set-password', ['token' => $request->token, 'email' => $request->email, 'title' => $title]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|confirmed',
        ]);

        googleCaptchaVerify($request);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) use ($request) {
                $user->password = Hash::make($password);
                $user->save();

                if ($user->email) {
                    sendTemplatedNotification(
                        $user->email,
                        'PASSWORD_RESET_SUCCESS',
                        [
                            'user_name'  => $user->name,
                            'changed_at' => now()->toDayDateTimeString(),
                            'login_url'  => route('login'),
                            'request_ip' => $request->ip(),
                        ]
                    );
                }
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}

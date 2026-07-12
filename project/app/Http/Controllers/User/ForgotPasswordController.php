<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function requestForm()
    {
        $title = __('Forgot Password');
        
        return theme('user.auth.forgot-password', compact('title'));
    }

    public function sendLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        googleCaptchaVerify($request);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->withSuccess(__("Reset link sent to your email."))
            : back()->withErrors(['email' => __($status)]);
    }
}

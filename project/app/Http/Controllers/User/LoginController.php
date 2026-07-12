<?php

namespace App\Http\Controllers\User;

use App\Helpers\BulkSmsHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function login()
    {
        $title = 'User login';

        return theme('user.auth.login', compact('title'));
    }

    public function otpLoginStore(Request $request)
    {
        $request->validate([
            'phone_number' => 'required'
        ]);

        $user = User::where('phone_number', $request->phone_number)->first();

        if (!$user) {
            return $request->ajax()
                ? response()->json(['message' => __('No user found with this phone number'), 'success' => false], 404)
                : back()->withErrors(__('No user found with this phone number'));
        }

        if ($user->otp_sent_at && $user->otp_sent_at->diffInSeconds(now()) < 120) {
            $remainingSeconds = 120 - $user->otp_sent_at->diffInSeconds(now());

            return $request->ajax()
                ? response()->json([
                    'message' => 'OTP already sent. Please wait before requesting a new one.',
                    'success' => false,
                    'remaining_seconds' => $remainingSeconds
                ])
                : back()->withErrors("OTP already sent. Please wait {$remainingSeconds} seconds.");
        }


        try {
            $code              = rand(100000, 999999);
            $user->otp_code    = $code;
            $user->otp_sent_at = now();
            $user->save();

            (new BulkSmsHelper())->send($request->phone_number, "Your one-time password is: {$code}");

            return $request->ajax()
                ? response()->json([
                    'message' => 'OTP sent successfully. Please check your phone.',
                    'success' => true,
                    'remaining_seconds' => 120
                ])
                : back()->withSuccess('OTP sent successfully. Please check your phone.');
        } catch (\Exception $e) {
            Log::error('SMS sending failed: ' . $e->getMessage());

            return $request->ajax()
                ? response()->json([
                    'message' => 'Failed to send OTP. Please check SMS configuration.',
                    'success' => false
                ], 500)
                : back()->withErrors('Failed to send OTP: ' . $e->getMessage());
        }
    }

    public function validateOtpAndLogin(Request $request)
    {
        $request->validate([
            'phone_number' => 'required',
            'otp_code'     => 'required|digits:6',
        ]);

        $user = User::where('phone_number', $request->phone_number)->first();

        if (!$user) {
            return $request->ajax()
                ? response()->json(['message' => 'User not found.', 'success' => false], 404)
                : back()->withErrors('User not found.');
        }

        // Check if OTP matches
        if ($user->otp_code !== $request->otp_code) {
            return $request->ajax()
                ? response()->json(['message' => 'Invalid OTP.', 'success' => false], 422)
                : back()->withErrors('Invalid OTP.');
        }

        // Check if OTP is expired (older than 2 minutes)
        if (!$user->otp_sent_at || now()->diffInSeconds($user->otp_sent_at) > 120) {
            return $request->ajax()
                ? response()->json(['message' => 'OTP has expired. Please request a new one.', 'success' => false], 410)
                : back()->withErrors('OTP has expired. Please request a new one.');
        }

        $agent   = \Browser::parse($request->userAgent() ?? '');
        $device  = $agent->deviceType();
        $browser = $agent->browserName();
        $os      = $agent->platformName();

        $ip = $request->ip();
        $city = $country = null;

        try {
            $response = Http::get("http://ip-api.com/json/{$ip}");
            if ($response->successful()) {
                $data = $response->json();
                $city = $data['city'] ?? null;
                $country = $data['country'] ?? null;
            }
        } catch (\Exception $e) {
            Log::warning('IP lookup failed: ' . $e->getMessage());
        }

        // OTP is valid, log in the user
        Auth::login($user);

        // Optionally, clear the OTP after successful login
        $user->otp_code = null;
        $user->otp_sent_at = null;
        $user->save();

        if ($user->email) {
            $location = trim(($city ? $city . ', ' : '') . ($country ?? '')) ?: __('Unknown');

            sendTemplatedNotification(
                $user->email,
                'USER_LOGIN_ALERT',
                [
                    'user_name' => $user->name,
                    'login_time' => now()->toDayDateTimeString(),
                    'login_ip' => $ip,
                    'login_device' => $device ?: __('Unknown Device'),
                    'login_location' => $location,
                ]
            );
        }

        return $request->ajax()
            ? response()->json(['message' => __('Logged in successfully.'), 'success' => true])
            : to_route('user.dashboard')->withSuccess(__('Logged in successfully.'));
    }

    public function loginStore(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        googleCaptchaVerify($request);

        if (Auth::attempt(['email' => $request->username, 'password' => $request->password])) {
            $user = Auth::user();

            // If user doesn’t have 2FA → normal login
            if (!$user->two_factor_secret || !$user->two_factor_confirmed_at) {
                   $agent   = \Browser::parse($request->userAgent() ?? '');
                    $device  = $agent->deviceType();
                    $browser = $agent->browserName();
                    $os      = $agent->platformName();
                
                $ip = $request->ip();
                $location = null;
                $city = $country = null;

                try {
                    $response = Http::get("http://ip-api.com/json/{$ip}");
                    if ($response->successful()) {
                        $data = $response->json();
                        $city = $data['city'] ?? null;
                        $country = $data['country'] ?? null;
                    }
                } catch (\Exception $e) {
                    Log::warning('IP lookup failed: ' . $e->getMessage());
                }
                
                 UserLogin::create([
                    'user_id' => $user->id,
                    'ip'      => $ip,
                    'city'    => $city,
                    'browser' => $browser,
                    'os'      => $os,
                    'country' => $country,
                ]);
                
                $location = trim(($city ? $city . ', ' : '') . ($country ?? '')) ?: __('Unknown');

                if ($user->email) {
                    sendTemplatedNotification(
                        $user->email,
                        'USER_LOGIN_ALERT',
                        [
                            'user_name' => $user->name,
                            'login_time' => now()->toDayDateTimeString(),
                            'login_ip' => $ip,
                            'login_device' => $device ?: __('Unknown Device'),
                            'login_location' => $location,
                        ]
                    );
                }

                return to_route('user.dashboard')->withSuccess(__('You are logged in'));
            }
            
            Auth::logout();

            $request->session()->put('2fa:user:id', $user->id);
            $request->session()->put('2fa:remember', $request->boolean('remember'));

            return redirect()->route('2fa.challenge');
        }

        return back()->withErrors([
            'username' => __('Invalid credentials.'),
        ])->withInput($request->only('username'));
    }

    public function logout()
    {
        Auth::guard('web')->logout();
        return to_route('home')->withSuccess(__('You are logged out'));
    }
}

<?php

namespace App\Http\Controllers\User\Auth;

use App\Helpers\BulkSmsHelper;
use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    protected $usernameField = 'email'; // can be email or phone

    public function register()
    {
        $title = generalSetting('user_registration') ? __('User Registration') : __('Registration is Disabled');

        if (!generalSetting('user_registration')) {
            return theme('user.auth.register_disabled', compact('title'));
        }

        return theme('user.auth.register', compact('title'));
    }

    public function registerStore(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', function ($attribute, $value, $fail) {
                if (!filter_var($value, FILTER_VALIDATE_EMAIL) && !preg_match('/^\+?[0-9]{10,15}$/', $value)) {
                    $fail('The ' . $attribute . ' must be a valid email or phone number.');
                }
            }],
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'password'   => 'required|string|min:6',
        ]);

        googleCaptchaVerify($request);

        $input = $request->email;

        $user = new User();
        $isPhone = false;

        // Check for email or phone
        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            if (User::where('email', $input)->exists()) {
                return back()->withErrors(['email' => 'Email already exists']);
            }
            $user->email = $input;
        } elseif (preg_match('/^\+?[0-9]{10,15}$/', $input)) {
            if (User::where('phone_number', $input)->exists()) {
                return back()->withErrors(['email' => 'Phone number already exists']);
            }
            $user->phone_number = $input;
            $isPhone = true;
        } else {
            return back()->withErrors(['email' => 'Invalid email or phone number']);
        }

        // Set name and password
        $user->first_name = $request->first_name;
        $user->last_name  = $request->last_name;
        $user->name       = $request->first_name . ' ' . $request->last_name;
        $user->password   = Hash::make($request->password);

        // KYC flag
        if (generalSetting('kyc')) {
            $user->kyc_required = true;
        }

        // Save user first
        $user->save();

        if ($user->email) {
            sendTemplatedNotification(
                $user->email,
                'USER_REGISTERED',
                [
                    'user_name'  => $user->name,
                    'user_email' => $user->email,
                    'login_url'  => route('login'),
                ]
            );
        }

        if (!$isPhone && method_exists($user, 'sendEmailVerificationNotification')) {
            $user->sendEmailVerificationNotification();
        }

        // Admin Notification
        $adminNotification          = new AdminNotification();
        $adminNotification->user_id = $user->id;
        $adminNotification->link    = route('admin.user.details', $user->id);
        $adminNotification->details = __('New user registered');
        $adminNotification->save();

        $adminRecipients = admin_notification_recipients();
        if (!empty($adminRecipients)) {
            sendTemplatedNotification(
                $adminRecipients,
                'ADMIN_NEW_USER_REGISTERED',
                [
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'user_registered_at' => $user->created_at->toDayDateTimeString(),
                    'user_profile_url' => route('admin.user.details', $user->id),
                ]
            );
        }

        // Login the user
        Auth::loginUsingId($user->id);

        // Redirect
        return $isPhone
            ? to_route('user.phone.verify.notice') // Show verify phone page
            : to_route('user.dashboard');          // If email, go straight to dashboard
    }
}

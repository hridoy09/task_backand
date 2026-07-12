<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\BulkSmsHelper;
use App\Services\FileService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use App\Facades\System;

class NotificationSettingController extends Controller
{
    public function notificationSetting()
    {
        goIfUserCan('view-settings.notifications');

        $title = __('Notifications Settings');

        $generalSetting = generalSetting();

        return view('admin.setting.notification', compact('title', 'generalSetting'));
    }

    public function updateNotificationSetting(Request $request, FileService $fileService)
    {
        goIfUserCan('save-settings.notifications');

        try {
            $validated = $request->validate([
                'sms_sender_id'     => 'nullable|string|max:255',
                'sms_api_key'       => 'nullable|string|max:255',
                'mail_host'         => 'nullable|string|max:255',
                'mail_port'         => 'nullable|string|max:255',
                'mail_username'     => 'nullable|string|max:255',
                'mail_password'     => 'nullable|string|max:255',
                'mail_encryption'   => 'nullable|string|max:255',
                'mail_from_address' => 'required|string|max:255',
                'mail_from_name'    => 'nullable|string|max:255',
                'current_mail_provider' => 'required|in:smtp,mailgun,ses,sendgrid,brevo,postmark,mailersend,sparkpost,mailjet,elastic_email,smtp_com,resend',


                 // Mailgun
                'mailgun_domain'         => 'nullable|string|max:255',
                'mailgun_secret'         => 'nullable|string|max:255',
                
                // SES
                'ses_access_key'         => 'nullable|string|max:255',
                'ses_secret_key'         => 'nullable|string|max:255',
                'ses_region'             => 'nullable|string|max:255',

                // SMS Providers
                'current_sms_provider'   => 'nullable|in:vonage,twilio,custom',
                
                // Nexmo
                'nexmo_api_key'          => 'nullable|string|max:255',
                'nexmo_api_secret'       => 'nullable|string|max:255',
                
                // Twilio
                'twilio_account_sid'     => 'nullable|string|max:255',
                'twilio_auth_token'      => 'nullable|string|max:255',
                'twilio_from_number'     => 'nullable|string|max:255',
                
                // Custom SMS
                'custom_sms_api_url'     => 'nullable|string|max:255',
                'custom_sms_api_user'    => 'nullable|string|max:255',
                'custom_sms_api_password'=> 'nullable|string|max:255',

                // SendGrid
                'sendgrid_api_key'       => 'nullable|string|max:255',

                // Brevo / Sendinblue
                'brevo_api_key'          => 'nullable|string|max:255',

                // Postmark
                'postmark_api_token'     => 'nullable|string|max:255',

                // MailerSend
                'mailersend_api_key'     => 'nullable|string|max:255',

                // SparkPost
                'sparkpost_api_key'      => 'nullable|string|max:255',

                // Mailjet
                'mailjet_api_key'        => 'nullable|string|max:255',
                'mailjet_secret_key'     => 'nullable|string|max:255',

                // Elastic Email
                'elastic_email_api_key'  => 'nullable|string|max:255',

                // SMTP.com
                'smtp_com_username'      => 'nullable|string|max:255',
                'smtp_com_password'      => 'nullable|string|max:255',

                // Resend
                'resend_api_key'         => 'nullable|string|max:255',
            ]);
        } catch (ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'errors' => $e->errors(),
                    'message' => 'Validation failed',
                    'success' => false,
                ], 422);
            }

            throw $e;
        }

        $setting = \App\Models\GeneralSetting::first();

        if (!$setting) {
            $setting = new \App\Models\GeneralSetting();
        }

        foreach ($validated as $key => $value) {
            $setting->$key = $value;
        }

        $setting->save();

        System::clearCache();

        if ($request->ajax()) {
            return response()->json([
                'message' => __('Notification saved successfully'),
                'success' => true
            ]);
        }

        return back()->withSuccess(__('Notification settings updated successfully.'));
    }

    public function sendTestMail(Request $request)
    {
        goIfUserCan('save-settings.notifications');

        try {
            $validated = $request->validate([
                'test_email'     => 'required|email|max:255',
            ]);
        } catch (ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'errors' => $e->errors(),
                    'message' => 'Validation failed',
                    'success' => false,
                ], 422);
            }

            throw $e;
        }

        try {
              $success = sendSystemNotification(
                    to: $validated['test_email'],
                    subject: 'This is a test email',
                    body: 'Hello dear, welcome to [app_name]!',
                    channel: 'email',   
                    // extraData: [
                    //     '[app_name]' => "xxxxxxxx Software"
                    // ]       
                );
            
            // Mail::raw('This is a test mail from your system.', function ($message) use ($validated) {
            //     $message->to($validated['test_email'])
            //         ->subject('Test Email from ' . config('app.name'));
            // });

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Test email sent successfully!',
                    'success' => true
                ]);
            }

            return back()->withSuccess('Test email sent successfully!');
        } catch (\Exception $e) {
            Log::error('Test mail failed: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Failed to send test email. Please check mail configuration.',
                    'success' => false,
                ], 500);
            }

            return back()->withErrors('Failed to send test email: ' . $e->getMessage());
        }
    }
    
    public function sendTestSMS(Request $request)
    {
        goIfUserCan('save-settings.notifications');

        try {
            $request->validate([
                'phone_number' => 'required|string|max:255',
                'message'      => 'required|string|max:255',
            ]);
        } catch (ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'errors' => $e->errors(),
                    'message' => 'Validation failed',
                    'success' => false,
                ], 422);
            }

            throw $e;
        }

        try {
            (new BulkSmsHelper())->send($request->phone_number, $request->message);

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Test SMS sent successfully!',
                    'success' => true
                ]);
            }

            return back()->withSuccess('Test SMS sent successfully!');
        } catch (\Exception $e) {
            Log::error('Test SMS failed: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'message' => 'Failed to send test SMS. Please check SMS configuration.',
                    'success' => false,
                ], 500);
            }

            return back()->withErrors('Failed to send test SMS: ' . $e->getMessage());
        }
    }
}

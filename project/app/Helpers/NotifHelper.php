<?php

use App\Services\MailTemplateService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

if (!function_exists('parseShortcodes')) {
    function parseShortcodes(?string $content, array $extraData = []): string
    {
        if ($content === null) {
            return '';
        }

        $shortcodes = MailTemplateService::defaultShortcodeReplacements();

        $shortcodes = array_merge($shortcodes, $extraData);

        return strtr($content, $shortcodes);
    }
}

if (!function_exists('sendSystemNotification')) {
    /**
     * Send notification via email or sms with multiple providers.
     */
    function sendSystemNotification(
        $to,
        ?string $subject,
        string $body,
        string $channel = 'email',
        array $extraData = [],
        string $view = 'global',
        array $attachments = []
    ): bool {
        try {
            $subject = $subject ? parseShortcodes($subject, $extraData) : null;
            $body = parseShortcodes($body, $extraData);

            $provider = $channel == 'email' ? generalSetting('current_mail_provider') : generalSetting('current_sms_provider');


            switch ($channel) {

                // ===== EMAIL =====
                case 'email':
                    // Dynamic email provider config
                    switch ($provider) {

                        // ===== Mailgun =====
                        case 'mailgun':
                            Config::set('mail.default', 'mailgun');
                            Config::set('mail.mailers.mailgun', [
                                'transport' => 'mailgun',
                                'domain'    => generalSetting('mailgun_domain'),
                                'secret'    => generalSetting('mailgun_secret'),
                            ]);
                            break;

                        // ===== Amazon SES =====
                        case 'ses':
                            Config::set('mail.default', 'ses');
                            Config::set('mail.mailers.ses', [
                                'transport' => 'ses',
                                'key'       => generalSetting('ses_access_key'),
                                'secret'    => generalSetting('ses_secret_key'),
                                'region'    => generalSetting('ses_region'),
                            ]);
                            break;

                        // ===== SendGrid =====
                        case 'sendgrid':
                            Config::set('mail.default', 'sendgrid');
                            Config::set('mail.mailers.sendgrid', [
                                'transport' => 'sendgrid',
                                'api_key'   => generalSetting('sendgrid_api_key'),
                            ]);
                            break;

                        // ===== Brevo / Sendinblue =====
                        case 'brevo':
                            Config::set('mail.default', 'brevo');
                            Config::set('mail.mailers.smtp', [
                                'transport'  => 'smtp',
                                'host'       => 'smtp-relay.brevo.com', // Brevo SMTP host
                                'port'       => 587,
                                'encryption' => 'tls',
                                'username'   => 'monirsaikat1@gmail.com', // usually your Brevo login/email
                                'password'   => generalSetting('brevo_api_key'), // your Brevo API key
                            ]);
                            // Config::set('mail.mailers.brevo', [
                            //     'transport' => 'brevo',
                            //     'api_key'   => generalSetting('brevo_api_key'),
                            // ]);
                            break;

                        // ===== Postmark =====
                        case 'postmark':
                            Config::set('mail.default', 'postmark');
                            Config::set('mail.mailers.postmark', [
                                'transport' => 'postmark',
                                'token'     => generalSetting('postmark_api_token'),
                            ]);
                            break;

                        // ===== MailerSend =====
                        case 'mailersend':
                            Config::set('mail.default', 'mailersend');
                            Config::set('mail.mailers.mailersend', [
                                'transport' => 'mailersend',
                                'api_key'   => generalSetting('mailersend_api_key'),
                            ]);
                            break;

                        // ===== SparkPost =====
                        case 'sparkpost':
                            Config::set('mail.default', 'sparkpost');
                            Config::set('mail.mailers.sparkpost.secret', generalSetting('sparkpost_api_key'));
                            break;

                        // ===== Mailjet =====
                        case 'mailjet':
                            Config::set('mail.default', 'mailjet');
                            Config::set('mail.mailers.mailjet', [
                                'transport' => 'mailjet',
                                'key'       => generalSetting('mailjet_api_key'),
                                'secret'    => generalSetting('mailjet_secret_key'),
                            ]);
                            break;

                        // ===== Elastic Email =====
                        case 'elastic_email':
                            Config::set('mail.default', 'elastic_email');
                            Config::set('mail.mailers.elastic_email', [
                                'transport' => 'elastic_email',
                                'api_key'   => generalSetting('elastic_email_api_key'),
                            ]);
                            break;

                        // ===== SMTP.com =====
                        case 'smtp_com':
                            Config::set('mail.default', 'smtp_com');
                            Config::set('mail.mailers.smtp_com', [
                                'transport' => 'smtp',
                                'username'  => generalSetting('smtp_com_username'),
                                'password'  => generalSetting('smtp_com_password'),
                                'host'      => generalSetting('mail_host'),
                                'port'      => generalSetting('mail_port'),
                                'encryption' => generalSetting('mail_encryption'),
                            ]);
                            break;

                        // ===== Resend =====
                        case 'resend':
                            Config::set('mail.default', 'resend');
                            Config::set('mail.mailers.resend', [
                                'transport' => 'resend',
                                'api_key'   => generalSetting('resend_api_key'),
                            ]);
                            break;

                        case 'php_mail':
                            Config::set('mail.default', 'mail');
                            Config::set('mail.mailers.mail', [
                                'transport' => 'mail',
                            ]);
                            break;

                        // ===== Default SMTP fallback =====
                        default:
                            Config::set('mail.default', 'smtp');
                            Config::set('mail.mailers.smtp', array_merge(
                                config('mail.mailers.smtp'),
                                [
                                    'host'       => generalSetting('mail_host'),
                                    'port'       => generalSetting('mail_port'),
                                    'username'   => generalSetting('mail_username'),
                                    'password'   => generalSetting('mail_password'),
                                    'encryption' => generalSetting('mail_encryption'),
                                ]
                            ));
                            break;
                    }

                    // Always set sender
                    Config::set('mail.from.address', generalSetting('mail_from_address'));
                    Config::set('mail.from.name', generalSetting('mail_from_name'));

                    // Send email
                    Mail::to($to)->send(
                        new \App\Mail\SystemNotification(
                            subjectLine: $subject,
                            viewName: $view,
                            data: array_merge(['mailBody' => $body], $extraData),
                            attachments: $attachments
                        )
                    );

                    break;
 
                // ===== SMS =====
                case 'sms':
                    switch ($provider) {
                        case 'twilio':
                            $sid   = generalSetting('twilio_account_sid');
                            $token = generalSetting('twilio_auth_token');
                            $from  = generalSetting('twilio_from_number');

                            $twilio = new \Twilio\Rest\Client($sid, $token);
                            $twilio->messages->create($to, [
                                'from' => $from,
                                'body' => $body,
                            ]);
                            break;

                        case 'nexmo':
                            $basic  = new \Vonage\Client\Credentials\Basic(
                                generalSetting('nexmo_api_key'),
                                generalSetting('nexmo_api_secret')
                            );
                            $client = new \Vonage\Client($basic);
                            $client->sms()->send(
                                new \Vonage\SMS\Message\SMS($to, generalSetting('nexmo_from'), $body)
                            );
                            break;

                        case 'custom':
                            $url      = generalSetting('custom_sms_api_url');
                            $user     = generalSetting('custom_sms_api_user');
                            $password = generalSetting('custom_sms_api_password');

                            if (!$url) {
                                throw new \Exception("Custom SMS API URL is not configured.");
                            }

                            // Simple cURL request
                            $payload = http_build_query([
                                'to'   => $to,
                                'msg'  => $body,
                                'user' => $user,
                                'pass' => $password,
                            ]);

                            $ch = curl_init($url);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            $response = curl_exec($ch);
                            curl_close($ch);
                            break;

                        default:
                            throw new \Exception("SMS provider [$provider] not supported.");
                    }
                    break;

                default:
                    throw new \Exception("Notification channel [$channel] not supported.");
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Notification failed: " . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('sendTemplatedNotification')) {
    function sendTemplatedNotification(
        $to,
        string $templateCode,
        array $shortcodes = [],
        string $channel = 'email',
        array $viewData = []
    ): bool {
        return \App\Services\MailTemplateService::sendUsingTemplate(
            recipients: $to,
            code: $templateCode,
            shortcodes: $shortcodes,
            channel: $channel,
            viewData: $viewData
        );
    }
}

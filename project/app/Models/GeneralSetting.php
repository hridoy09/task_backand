<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralSetting extends Model
{
    protected $casts = [
        'global_seo' => 'array',
    ];
    
    protected $fillable = [
        'global_seo',
        'sendgrid_api_key',
        'brevo_api_key',
        'postmark_api_token',
        'mailersend_api_key',
        'sparkpost_api_key',
        'mailjet_api_key',
        'mailjet_secret_key',
        'elastic_email_api_key',
        'smtp_com_username',
        'smtp_com_password',
        'resend_api_key',
        'mailgun_domain',
        'mailgun_secret',
        'ses_access_key',
        'ses_secret_key',
        'ses_region',
        'current_sms_provider',
        'nexmo_api_key',
        'nexmo_api_secret',
        'twilio_account_sid',
        'twilio_auth_token',
        'twilio_from_number',
        'custom_sms_api_url',
        'custom_sms_api_user',
        'custom_sms_api_password',
        'current_mail_provider',
        'demo_mode',
        'site_title',
        'site_description',
        'site_email',
        'site_phone',
        'software_version',
        'site_logo',
        'site_favicon',
        'currency',
        'default_paginate',
        'timezone',
        'sms_sender_id',
        'sms_api_key',
        'app_env',
        'app_url',
        'force_ssl',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_from_address',
        'mail_from_name',
    ];

    protected static function booted()
    {
        static::saved(function ($model) {
            if (function_exists('software')) {
                software()->clearCache();
            }
        });
    }

}

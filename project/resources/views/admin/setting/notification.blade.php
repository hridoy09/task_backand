@extends('admin.layouts.settings')

@push('styles')
    <style>
        .notification-card {
            border: 1px solid hsl(var(--theme-color) / .15);
            border-radius: 12px;
            overflow: hidden;
        }

        .ntf-tabs {
            display: inline-flex;
            gap: 8px;
            padding: 6px;
            border: 1px solid hsl(var(--border-color));
            border-radius: 10px;
            background: hsl(var(--sec-bg-color));
            margin-bottom: 1rem;
        }

        .ntf-tab-btn {
            border: 1px solid transparent;
            background: transparent;
            color: hsl(var(--dark-color));
            padding: .45rem .95rem;
            border-radius: 8px;
            font-weight: 600;
        }

        .ntf-tab-btn.active {
            border-color: hsl(var(--theme-color) / .25);
            background: hsl(var(--white-color));
            color: hsl(var(--theme-color));
        }

        .ntf-panel {
            display: none;
        }

        .ntf-panel.active {
            display: block;
        }

        .config-section {
            padding-top: 2rem;
            margin-top: 2rem;
            border-top: 1px solid hsl(var(--border-color));
        }

        .section-header {
            margin-bottom: 1.5rem;
            padding: 1rem 1.25rem;
            border: 1px solid hsl(var(--border-color));
            border-radius: 10px;
            background: hsl(var(--sec-bg-color));
        }

        .section-header h5 {
            font-size: 1.05rem;
            font-weight: 600;
            margin-bottom: 0;
        }

        .provider-settings {
            border: 1px solid hsl(var(--theme-color) / .15);
            border-radius: 10px;
            background: hsl(var(--white-color));
            padding: 1.5rem;
            margin-top: 1.5rem;
        }

        .provider-settings h6 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: .5rem;
            color: hsl(var(--dark-color));
        }

        .provider-settings hr {
            margin: .75rem 0 1.25rem;
            border-color: hsl(var(--border-color));
        }

        .form-group label {
            font-weight: 600;
        }

        .ntf-actions {
            border-top: 1px solid hsl(var(--border-color));
            padding-top: 1rem;
        }

        .modal-content {
            border: 1px solid hsl(var(--border-color));
            border-radius: 12px;
        }

        @media (max-width: 768px) {
            .section-header {
                padding: .85rem 1rem;
                gap: .75rem !important;
                align-items: flex-start !important;
                flex-direction: column;
            }

            .provider-settings {
                padding: 1rem;
            }

            .ntf-actions {
                justify-content: stretch !important;
            }

            .ntf-actions .btn,
            .ntf-actions button {
                width: 100%;
            }
        }
    </style>
@endpush

@section('panel')
    <div class="manage-section-card-form">
        <div class="manage-section-card mb-32">
            <div class="row">
                <div class="col-lg-12">
                    <form class="ajax-form" action="{{ route('admin.setting.notification.update') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="ntf-tabs" role="tablist" aria-label="@lang('Notification Type Tabs')">
                            <button type="button" class="ntf-tab-btn active"
                                data-tab-target="email">@lang('Email')</button>
                            <button type="button" class="ntf-tab-btn" data-tab-target="sms">@lang('SMS')</button>
                        </div>

                        <div class="card notification-card mb-3 ntf-panel active" data-tab-panel="email">
                            <div class="card-header">
                                <h4 class="card-header__title">@lang('Email Notification Setting')</h4>
                            </div>
                            <div class="card-body">
                                {{-- Email Configuration Section --}}
                                <div class="section-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">@lang('Email Provider Configuration')</h5>
                                    <button class="btn btn-outline-theme" id="sendTestMailBtn" type="button">
                                        <x-icons.send />
                                        @lang('Send Test Mail')
                                    </button>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <x-form.group for="mail_from_address" label="From Address">
                                            <input required type="text" name="mail_from_address" id="mail_from_address"
                                                class="form-control"
                                                value="{{ old('mail_from_address', $generalSetting['mail_from_address']) }}">
                                        </x-form.group>
                                    </div>
                                    <div class="col-md-4">
                                        <x-form.group for="mail_from_name" label="From Name">
                                            <input type="text" name="mail_from_name" id="mail_from_name"
                                                class="form-control"
                                                value="{{ old('mail_from_name', $generalSetting['mail_from_name']) }}">
                                        </x-form.group>
                                    </div>
                                    <div class="col-md-4">
                                        <x-form.group for="current_mail_provider" label="Mail Provider">
                                            <select name="current_mail_provider " id="current_mail_provider"
                                                class="form-control js-select2" >
                                                <option @selected($generalSetting['current_mail_provider'] == 'smtp') value="smtp">@lang('SMTP')
                                                </option>
                                                <option @selected($generalSetting['current_mail_provider'] == 'mailgun') value="mailgun">@lang('Mailgun')
                                                </option>
                                                <option @selected($generalSetting['current_mail_provider'] == 'ses') value="ses">@lang('SES')
                                                </option>
                                                <option @selected($generalSetting['current_mail_provider'] == 'sendgrid') value="sendgrid">@lang('SendGrid')
                                                </option>
                                                <option @selected($generalSetting['current_mail_provider'] == 'brevo') value="brevo">@lang('Brevo')
                                                </option>
                                                <option @selected($generalSetting['current_mail_provider'] == 'postmark') value="postmark">@lang('Postmark')
                                                </option>
                                                <option @selected($generalSetting['current_mail_provider'] == 'mailersend') value="mailersend">@lang('MailerSend')
                                                </option>
                                                <option @selected($generalSetting['current_mail_provider'] == 'sparkpost') value="sparkpost">@lang('SparkPost')
                                                </option>
                                                <option @selected($generalSetting['current_mail_provider'] == 'mailjet') value="mailjet">@lang('Mailjet')
                                                </option>
                                                <option @selected($generalSetting['current_mail_provider'] == 'elastic_email') value="elastic_email">
                                                    @lang('Elastic Email')</option>
                                                <option @selected($generalSetting['current_mail_provider'] == 'smtp_com') value="smtp_com">@lang('SMTP.com')
                                                </option>
                                                <option @selected($generalSetting['current_mail_provider'] == 'resend') value="resend">@lang('Resend')
                                                </option>
                                            </select>
                                        </x-form.group>

                                    </div>
                                </div>

                                {{-- SMTP Settings --}}
                                <div id="smtp_settings" class="provider-settings mail-provider-config">
                                    <h6>@lang('SMTP Configuration')</h6>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <x-form.group label="Mail Host" for="mail_host">
                                                <input type="text" name="mail_host" class="form-control"
                                                    value="{{ old('mail_host', $generalSetting['mail_host'] ?? '') }}">
                                            </x-form.group>
                                        </div>
                                        <div class="col-md-4">
                                            <x-form.group label="Mail Port" for="mail_port">
                                                <input type="text" name="mail_port" class="form-control"
                                                    value="{{ old('mail_port', $generalSetting['mail_port'] ?? '') }}">
                                            </x-form.group>
                                        </div>
                                        <div class="col-md-4">
                                            <x-form.group label="Mail Username" for="mail_username">
                                                <input type="text" name="mail_username" class="form-control"
                                                    value="{{ old('mail_username', $generalSetting['mail_username'] ?? '') }}">
                                            </x-form.group>
                                        </div>
                                        <div class="col-md-4">
                                            <x-form.group label="Mail Password" for="mail_password">
                                                <input type="password" name="mail_password" class="form-control"
                                                    value="{{ old('mail_password', $generalSetting['mail_password'] ?? '') }}">
                                            </x-form.group>
                                        </div>
                                        <div class="col-md-4">
                                            <x-form.group label="Mail Encryption" for="mail_encryption">
                                                <select class="form-control js-select2" data-search="false"  name="mail_encryption">
                                                    <option value="ssl" @selected(@$generalSetting['mail_encryption'] == 'ssl')>@lang('SSL')
                                                    </option>
                                                    <option value="tls" @selected(@$generalSetting['mail_encryption'] == 'tls')>@lang('TLS')
                                                    </option>
                                                </select>
                                            </x-form.group>
                                        </div>
                                    </div>
                                </div>

                                {{-- Mailgun Settings --}}
                                <div id="mailgun_settings" class="provider-settings mail-provider-config">
                                    <h6>@lang('Mailgun Configuration')</h6>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <x-form.group label="Mailgun Domain" for="mailgun_domain">
                                                <input type="text" name="mailgun_domain" class="form-control"
                                                    value="{{ old('mailgun_domain', $generalSetting['mailgun_domain'] ?? '') }}">
                                            </x-form.group>
                                        </div>
                                        <div class="col-md-6">
                                            <x-form.group label="Mailgun Secret" for="mailgun_secret">
                                                <input type="text" name="mailgun_secret" class="form-control"
                                                    value="{{ old('mailgun_secret', $generalSetting['mailgun_secret'] ?? '') }}">
                                            </x-form.group>
                                        </div>
                                    </div>
                                </div>

                                {{-- SES Settings --}}
                                <div id="ses_settings" class="provider-settings mail-provider-config">
                                    <h6>@lang('Amazon SES Configuration')</h6>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <x-form.group label="SES Access Key" for="ses_access_key">
                                                <input type="text" name="ses_access_key" class="form-control"
                                                    value="{{ old('ses_access_key', $generalSetting['ses_access_key'] ?? '') }}">
                                            </x-form.group>
                                        </div>
                                        <div class="col-md-4">
                                            <x-form.group label="SES Secret Key" for="ses_secret_key">
                                                <input type="text" name="ses_secret_key" class="form-control"
                                                    value="{{ old('ses_secret_key', $generalSetting['ses_secret_key'] ?? '') }}">
                                            </x-form.group>
                                        </div>
                                        <div class="col-md-4">
                                            <x-form.group label="SES Region" for="ses_region">
                                                <input type="text" name="ses_region" class="form-control"
                                                    value="{{ old('ses_region', $generalSetting['ses_region'] ?? '') }}">
                                            </x-form.group>
                                        </div>
                                    </div>
                                </div>

                                {{-- SendGrid Settings --}}
                                <div id="sendgrid_settings" class="provider-settings mail-provider-config">
                                    <h6>@lang('SendGrid Configuration')</h6>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <x-form.group label="SendGrid API Key" for="sendgrid_api_key">
                                                <input type="text" name="sendgrid_api_key" class="form-control"
                                                    value="{{ old('sendgrid_api_key', $generalSetting['sendgrid_api_key'] ?? '') }}">
                                            </x-form.group>
                                        </div>
                                    </div>
                                </div>

                                {{-- Brevo / Sendinblue Settings --}}
                                <div id="brevo_settings" class="provider-settings mail-provider-config">
                                    <h6>@lang('Brevo / Sendinblue Configuration')</h6>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <x-form.group label="Brevo API Key" for="brevo_api_key">
                                                <input type="text" name="brevo_api_key" class="form-control"
                                                    value="{{ old('brevo_api_key', $generalSetting['brevo_api_key'] ?? '') }}">
                                            </x-form.group>
                                        </div>
                                    </div>
                                </div>

                                {{-- Postmark Settings --}}
                                <div id="postmark_settings" class="provider-settings mail-provider-config">
                                    <h6>@lang('Postmark Configuration')</h6>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <x-form.group label="Postmark API Token" for="postmark_api_token">
                                                <input type="text" name="postmark_api_token" class="form-control"
                                                    value="{{ old('postmark_api_token', $generalSetting['postmark_api_token'] ?? '') }}">
                                            </x-form.group>
                                        </div>
                                    </div>
                                </div>

                                {{-- MailerSend Settings --}}
                                <div id="mailersend_settings" class="provider-settings mail-provider-config">
                                    <h6>@lang('MailerSend Configuration')</h6>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <x-form.group label="MailerSend API Key" for="mailersend_api_key">
                                                <input type="text" name="mailersend_api_key" class="form-control"
                                                    value="{{ old('mailersend_api_key', $generalSetting['mailersend_api_key'] ?? '') }}">
                                            </x-form.group>
                                        </div>
                                    </div>
                                </div>

                                {{-- SparkPost Settings --}}
                                <div id="sparkpost_settings" class="provider-settings mail-provider-config">
                                    <h6>@lang('SparkPost Configuration')</h6>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <x-form.group label="SparkPost API Key" for="sparkpost_api_key">
                                                <input type="text" name="sparkpost_api_key" class="form-control"
                                                    value="{{ old('sparkpost_api_key', $generalSetting['sparkpost_api_key'] ?? '') }}">
                                            </x-form.group>
                                        </div>
                                    </div>
                                </div>

                                {{-- Mailjet Settings --}}
                                <div id="mailjet_settings" class="provider-settings mail-provider-config">
                                    <h6>@lang('Mailjet Configuration')</h6>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <x-form.group label="Mailjet API Key" for="mailjet_api_key">
                                                <input type="text" name="mailjet_api_key" class="form-control"
                                                    value="{{ old('mailjet_api_key', $generalSetting['mailjet_api_key'] ?? '') }}">
                                            </x-form.group>
                                        </div>
                                        <div class="col-md-6">
                                            <x-form.group label="Mailjet Secret Key" for="mailjet_secret_key">
                                                <input type="text" name="mailjet_secret_key" class="form-control"
                                                    value="{{ old('mailjet_secret_key', $generalSetting['mailjet_secret_key'] ?? '') }}">
                                            </x-form.group>
                                        </div>
                                    </div>
                                </div>

                                {{-- Elastic Email Settings --}}
                                <div id="elastic_email_settings" class="provider-settings mail-provider-config">
                                    <h6>@lang('Elastic Email Configuration')</h6>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <x-form.group label="Elastic Email API Key" for="elastic_email_api_key">
                                                <input type="text" name="elastic_email_api_key" class="form-control"
                                                    value="{{ old('elastic_email_api_key', $generalSetting['elastic_email_api_key'] ?? '') }}">
                                            </x-form.group>
                                        </div>
                                    </div>
                                </div>

                                {{-- SMTP.com Settings --}}
                                <div id="smtp_com_settings" class="provider-settings mail-provider-config">
                                    <h6>@lang('SMTP.com Configuration')</h6>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <x-form.group label="SMTP.com Username" for="smtp_com_username">
                                                <input type="text" name="smtp_com_username" class="form-control"
                                                    value="{{ old('smtp_com_username', $generalSetting['smtp_com_username'] ?? '') }}">
                                            </x-form.group>
                                        </div>
                                        <div class="col-md-6">
                                            <x-form.group label="SMTP.com Password" for="smtp_com_password">
                                                <input type="password" name="smtp_com_password" class="form-control"
                                                    value="{{ old('smtp_com_password', $generalSetting['smtp_com_password'] ?? '') }}">
                                            </x-form.group>
                                        </div>
                                    </div>
                                </div>

                                {{-- Resend Settings --}}
                                <div id="resend_settings" class="provider-settings mail-provider-config">
                                    <h6>@lang('Resend Configuration')</h6>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <x-form.group label="Resend API Key" for="resend_api_key">
                                                <input type="text" name="resend_api_key" class="form-control"
                                                    value="{{ old('resend_api_key', $generalSetting['resend_api_key'] ?? '') }}">
                                            </x-form.group>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card notification-card ntf-panel" data-tab-panel="sms">
                            <div class="card-header">
                                <h4 class="card-header__title">@lang('SMS Notification Setting')</h4>
                            </div>
                            <div class="card-body">
                                {{-- SMS Configuration Section --}}
                                <div>
                                    <div class="section-header d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">@lang('SMS Provider Configuration')</h5>
                                        <button class="btn btn-outline-theme" id="testSMSBtn"
                                            type="button">@lang('Send Test SMS')</button>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <x-form.group for="current_sms_provider" label="SMS Provider">
                                                <select name="current_sms_provider" id="current_sms_provider"
                                                    class="form-control js-select2" data-search="false" >
                                                    <option @selected(@$generalSetting['current_sms_provider'] == 'vonage') value="vonage">@lang('Nexmo (Vonage)')
                                                    </option>
                                                    <option @selected(@$generalSetting['current_sms_provider'] == 'twilio') value="twilio">@lang('Twilio')
                                                    </option>
                                                    <option @selected(@$generalSetting['current_sms_provider'] == 'custom') value="custom">@lang('Custom Provider')
                                                    </option>
                                                </select>
                                            </x-form.group>
                                        </div>
                                    </div>

                                    {{-- Vonage (Nexmo) Settings --}}
                                    <div id="vonage_settings" class="provider-settings sms-provider-config">
                                        <h6>@lang('Nexmo (Vonage) Configuration')</h6>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <x-form.group label="Nexmo API Key" for="nexmo_api_key">
                                                    <input type="text" name="nexmo_api_key" class="form-control"
                                                        value="{{ old('nexmo_api_key', @$generalSetting['nexmo_api_key']) }}">
                                                </x-form.group>
                                            </div>
                                            <div class="col-md-6">
                                                <x-form.group label="Nexmo API Secret" for="nexmo_api_secret">
                                                    <input type="text" name="nexmo_api_secret" class="form-control"
                                                        value="{{ old('nexmo_api_secret', @$generalSetting['nexmo_api_secret']) }}">
                                                </x-form.group>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Twilio Settings --}}
                                    <div id="twilio_settings" class="provider-settings sms-provider-config">
                                        <h6>@lang('Twilio Configuration')</h6>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <x-form.group label="Twilio Account SID" for="twilio_account_sid">
                                                    <input type="text" name="twilio_account_sid" class="form-control"
                                                        value="{{ old('twilio_account_sid', @$generalSetting['twilio_account_sid']) }}">
                                                </x-form.group>
                                            </div>
                                            <div class="col-md-6">
                                                <x-form.group label="Twilio Auth Token" for="twilio_auth_token">
                                                    <input type="text" name="twilio_auth_token" class="form-control"
                                                        value="{{ old('twilio_auth_token', @$generalSetting['twilio_auth_token']) }}">
                                                </x-form.group>
                                            </div>
                                            <div class="col-md-6">
                                                <x-form.group label="Twilio From Number" for="twilio_from_number">
                                                    <input type="text" name="twilio_from_number" class="form-control"
                                                        value="{{ old('twilio_from_number', @$generalSetting['twilio_from_number']) }}">
                                                </x-form.group>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Custom SMS Settings --}}
                                    <div id="custom_settings" class="provider-settings sms-provider-config">
                                        <h6>@lang('Custom SMS API Configuration')</h6>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <x-form.group label="API URL" for="custom_sms_api_url">
                                                    <input type="text" name="custom_sms_api_url" class="form-control"
                                                        value="{{ old('custom_sms_api_url', @$generalSetting['custom_sms_api_url']) }}">
                                                </x-form.group>
                                            </div>
                                            <div class="col-md-6">
                                                <x-form.group label="API Username" for="custom_sms_api_user">
                                                    <input type="text" name="custom_sms_api_user" class="form-control"
                                                        value="{{ old('custom_sms_api_user', @$generalSetting['custom_sms_api_user']) }}">
                                                </x-form.group>
                                            </div>
                                            <div class="col-md-6">
                                                <x-form.group label="API Password" for="custom_sms_api_password">
                                                    <input type="password" name="custom_sms_api_password"
                                                        class="form-control"
                                                        value="{{ old('custom_sms_api_password', @$generalSetting['custom_sms_api_password']) }}">
                                                </x-form.group>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-outline-theme">
                                <x-icons.save />
                                @lang('Save Changes')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Test Mail Modal --}}
    <div class="modal fade" id="testEmailSenderModal" tabindex="-1" aria-labelledby="testEmailSenderModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="testEmailSenderModalLabel">@lang('Send Test Email')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
                </div>
                <form class="ajax-form-mail" action="{{ route('admin.setting.notification.test_mail') }}"
                    method="POST">
                    @csrf
                    <div class="modal-body">
                        <x-form.group for="test_email" label="Email Address">
                            <input required type="email" name="test_email" class="form-control"
                                placeholder="@lang('Enter email address')" />
                        </x-form.group>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-outline-theme"><i class="fas fa-paper-plane"></i>@lang('Send')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Test SMS Modal --}}
    <div class="modal fade" id="testSmsModal" tabindex="-1" aria-labelledby="testSmsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="testSmsModalLabel">@lang('Send Test SMS')</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="fas fa-times"></i></button>
                </div>
                <form class="ajax-form-sms" action="{{ route('admin.setting.notification.test_sms') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <x-form.group for="phone_number" label="Phone Number">
                            <input type="text" name="phone_number" required class="form-control"
                                placeholder="@lang('e.g. +14155552671')" />
                        </x-form.group>
                        <x-form.group for="message" label="Message">
                            <textarea required name="message" class="form-control" placeholder="@lang('Your test message')"></textarea>
                        </x-form.group>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-outline-theme"><i class="fas fa-paper-plane"></i>@lang('Send')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        'use strict';

        (function($) {
            $(document).ready(function() {


                // --- Tab Switch ---
                function activateNotificationTab(tabKey) {
                    $('.ntf-tab-btn').removeClass('active');
                    $('.ntf-tab-btn[data-tab-target="' + tabKey + '"]').addClass('active');

                    $('.ntf-panel').removeClass('active');
                    $('.ntf-panel[data-tab-panel="' + tabKey + '"]').addClass('active');
                }

                $(document).on('click', '.ntf-tab-btn', function() {
                    activateNotificationTab($(this).data('tab-target'));
                });

                // default tab: email
                activateNotificationTab('email');

                // --- Modal Triggers ---
                $('#sendTestMailBtn').on('click', () => $('#testEmailSenderModal').modal('show'));
                $('#testSMSBtn').on('click', () => $('#testSmsModal').modal('show'));

                // --- Dynamic Provider Settings ---
                function toggleProviderSettings(selector, configClass) {
                    const selectedValue = $(selector).val();
                    $(`.${configClass}`).hide();
                    $(`#${selectedValue}_settings`).show();
                }

                // Mail provider toggle
                $('#current_mail_provider').on('change', function() {
                    toggleProviderSettings(this, 'mail-provider-config');
                }).trigger('change');

                // SMS provider toggle
                $('#current_sms_provider').on('change', function() {
                    toggleProviderSettings(this, 'sms-provider-config');
                }).trigger('change');

            });
        })(jQuery);
    </script>
@endpush

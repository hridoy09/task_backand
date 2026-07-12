<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_title')->default('Site title');
            $table->string('site_description')->nullable()->default('A short description of the site.');
            $table->string('app_url')->nullable();
            $table->string('site_logo')->nullable();
            $table->string('site_favicon')->nullable();
            $table->string('site_email')->default('contact@domain.com');
            $table->string('site_phone')->nullable();
            $table->string('software_version')->default('1.0.0');
            $table->timestamps();
            $table->string('currency')->nullable()->comment('site currency');
            $table->unsignedInteger('default_paginate')->default(10)->comment('default paginate length for tables');
            $table->boolean('google_recaptcha_enabled')->default(false);
            $table->tinyInteger('user_registration')->default(1)->comment('User registration done');
            $table->tinyInteger('kyc')->default(1)->comment('KYC is required for new users');
            $table->boolean('maintenance_mode')->default(false)->comment('Maintenance mode enabled or not');
            $table->string('timezone')->nullable();
            $table->integer('force_ssl')->default(0);
            $table->string('sms_sender_id')->nullable();
            $table->string('sms_api_key')->nullable();
            $table->string('mail_host')->nullable();
            $table->string('mail_port')->nullable();
            $table->string('mail_username')->nullable();
            $table->string('mail_password')->nullable();
            $table->string('mail_encryption')->nullable();
            $table->string('mail_from_address')->nullable();
            $table->string('mail_from_name')->nullable();
            $table->string('admin_prefix')->nullable();
            $table->string('user_prefix')->nullable();
            $table->string('app_env')->nullable();
            $table->boolean('user_api')->default(true)->comment('user api is enabled or not');
            $table->boolean('demo_mode')->default(false);
            $table->string('current_mail_provider')->nullable();
            $table->string('mailgun_domain')->nullable();
            $table->string('mailgun_secret')->nullable();
            $table->string('ses_access_key')->nullable();
            $table->string('ses_secret_key')->nullable();
            $table->string('ses_region')->nullable();
            $table->string('current_sms_provider')->nullable();
            $table->string('nexmo_api_key')->nullable();
            $table->string('nexmo_api_secret')->nullable();
            $table->string('twilio_account_sid')->nullable();
            $table->string('twilio_auth_token')->nullable();
            $table->string('twilio_from_number')->nullable();
            $table->string('custom_sms_api_url')->nullable();
            $table->string('custom_sms_api_user')->nullable();
            $table->string('custom_sms_api_password')->nullable();
            $table->string('sendgrid_api_key')->nullable();
            $table->string('brevo_api_key')->nullable();
            $table->string('postmark_api_token')->nullable();
            $table->string('mailersend_api_key')->nullable();
            $table->string('sparkpost_api_key')->nullable();
            $table->string('mailjet_api_key')->nullable();
            $table->string('mailjet_secret_key')->nullable();
            $table->string('elastic_email_api_key')->nullable();
            $table->string('smtp_com_username')->nullable();
            $table->string('smtp_com_password')->nullable();
            $table->string('resend_api_key')->nullable();
            $table->string('current_theme')->default('primary');
        });

        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->boolean('status')->default(true)->comment('1=active,0=inactive');
            $table->timestamps();
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->text('title');
            $table->text('slug');
            $table->tinyInteger('is_default')->default(0)->comment('Is default or not for the system');
            $table->text('seo_content')->nullable();
            $table->string('seo_image')->nullable();
            $table->text('sections')->nullable();
            $table->boolean('privacy')->default(false);
            $table->text('content')->nullable();
            $table->timestamps();
        });

        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->unsignedBigInteger('views')->default(0);
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->text('value');
            $table->timestamps();
        });

        Schema::create('site_visits', function (Blueprint $table) {
            $table->id();
            $table->string('ip');
            $table->date('visit_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_visits');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('page_views');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('languages');
        Schema::dropIfExists('general_settings');
    }
};

<?php

namespace Database\Seeders;

use App\Models\GeneralSetting;
use Illuminate\Database\Seeder;

class GeneralSettingSeeder extends Seeder
{
    public function run(): void
    {
        GeneralSetting::updateOrCreate(
            ['id' => 1],
            [
                'site_title'               => 'LaraKit',
                'site_description'         => 'All-in-one admin starter kit.',
                'app_url'                  => config('app.url', 'http://localhost'),
                'site_logo'                => '/assets/images/logo-icon/logo.png',
                'site_logo_dark'           => '/assets/images/logo-icon/logo_dark.png',
                'site_favicon'             => '/assets/images/logo-icon/favicon.png',
                'site_email'               => 'contact@example.com',
                'site_phone'               => '0000000000',
                'software_version'         => '1.0.0',
                'currency'                 => 'USD',
                'default_paginate'         => 15,
                'google_recaptcha_enabled' => false,
                'user_registration'        => 1,
                'kyc'                      => 1,
                'maintenance_mode'         => 0,
                'timezone'                 => 'UTC',
                'force_ssl'                => 0,
                'mail_host'                => 'smtp.mailtrap.io',
                'mail_port'                => '2525',
                'mail_username'            => null,
                'mail_password'            => null,
                'mail_encryption'          => 'tls',
                'mail_from_address'        => 'noreply@example.com',
                'mail_from_name'           => 'LaraKit',
                'admin_prefix'             => 'admin',
                'user_prefix'              => 'user',
                'app_env'                  => 'local',
                'user_api'                 => 1,
                'demo_mode'                => 0,
                'current_theme'            => 'primary',
                'global_seo' => (object)[
                    'meta_keywords' => ['larakit', 'software', 'starter kit', 'php laravel'],
                    'meta_description' => 'This is the starter kit of laravel software framework',
                    'social_title' => 'Larakit - a feature rich laravel software framework',
                    'social_description' => 'Larakit - a feature rich laravel software framework',
                    'meta_title' => 'LaraKit',
                    'image' => null,
                ],
            ]
        );
    }
}

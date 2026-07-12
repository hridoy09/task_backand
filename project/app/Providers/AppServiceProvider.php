<?php

namespace App\Providers;

use App\Helpers\SystemHelper;
use App\Models\Admin;
use App\Models\User;
use App\Services\MailTemplateService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Silber\Bouncer\BouncerFacade;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('system', function () {
            return new SystemHelper();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // if(env('LICENSE') != 'ok') {
        //     header('Location: installer/index.php');
        //     exit;
        // }
        
        BouncerFacade::useUserModel(Admin::class);

        $databaseReady = $this->databaseIsReady();

        $theme = $databaseReady ? (generalSetting('current_theme') ?? 'primary') : 'primary';

        View::addNamespace('theme', resource_path("views/themes/{$theme}"));

        // Config::set('app.url', generalSetting('app_url'));
        // Config::set('app.asset_url', generalSetting('app_url'));
        // Config::set('app.env', generalSetting('app_env'));
        // Config::set('app.debug_mode', true);

        if ($databaseReady) {
            MailTemplateService::syncConfiguredTemplates();

            $this->app->booted(function () {
                $timezone = generalSetting('timezone') ?? 'UTC';
                date_default_timezone_set($timezone);
                config(['app.timezone' => $timezone]);

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

                Config::set('mail.from.address', generalSetting('mail_from_address'));
                Config::set('mail.from.name', generalSetting('mail_from_name'));
            });

            viewShare('admin.partials.sidebar', [
                'kycPendingUsers' => User::kycPending()->count(),
                'emailUnverifiedUsers' => User::emailUnverified()->count(),
                'newUsers' => User::new()->count(),
            ]);
        }

        Paginator::useBootstrapFive();

        if ($databaseReady && generalSetting('force_ssl')) {
            URL::forceHttps();
        }
    }

    protected function databaseIsReady(): bool
    {
        try {
            return Schema::hasTable('general_settings')
                && Schema::hasTable('users')
                && Schema::hasTable('mail_templates');
        } catch (\Throwable $e) {
            return false;
        }
    }
}

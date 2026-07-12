<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \App\Events\AdminLoggedIn::class => [
            \App\Listeners\LogAdminLogin::class, // implements ShouldQueue
        ],
        // \App\Events\AdminLoginFailed::class => [\App\Listeners\LogAdminFailed::class],
    ];

    public function boot(): void
    {
        //
    }
}

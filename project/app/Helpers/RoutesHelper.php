<?php

namespace App\Helpers;

use Closure;
use Illuminate\Support\Facades\Route;

class RoutesHelper
{
    /**
     * Controller Namespace for routes
     * @var string
     */
    private static $controllerNamespace = 'App\Http\Controllers\\';

    /**
     * You can change admin routes prefix, default to admin
     * @var string
     */
    private static $adminPrefix = null;

    /**
     * You can change user routes prefix, default to user
     * @var string
     */
    private static $userPrefix = null;

    /**
     * Register routes for users, only authencated. Can be passed extra middlewares
     * @param \Closure $callback
     * @param mixed $middlewares
     * @return void
     */
    public static function registerUserRoutes(Closure $callback, $middlewares = [])
    {
        Route::middleware(array_merge(['auth'], $middlewares))
            ->group(function () use ($callback) {
                $callback();
            });
    }

    /**
     * Register routes for admin, only authencated. Can be passed extra middlewares
     * @param \Closure $callback
     * @param mixed $middlewares
     * @return void
     */
    public static function registerAdminRoutes(Closure $callback, $middlewares = [])
    {
        Route::middleware(array_merge(['admin'], $middlewares))
            ->group(function () use ($callback) {
                $callback();
            });
    }

    /**
     * Setup user routes
     * @return void
     */
    private static function setupUserRoutes()
    {
        Route::middleware(['web', 'maintenace'])
            ->prefix(self::$userPrefix ?? trim(generalSetting('user_prefix') ?? 'user', '/'))
            ->namespace(self::$controllerNamespace . 'User')
            ->name('user.')
            ->group(base_path('routes/user.php'));
    }

    /**
     * Setup admin routes
     * @return void
     */
    private static function setupAdminRoutes()
    {
        Route::middleware(['web'])
            ->prefix(self::$adminPrefix ?? trim(generalSetting('admin_prefix') ?? 'admin', '/'))
            ->namespace(self::$controllerNamespace . 'Admin')
            ->name('admin.')
            ->group(base_path('routes/admin.php'));
    }

    /**
     * Setup routes
     * @return void
     */
    public static function setupRoutes()
    {
        if (config('system.routes.user')) {
            self::setupUserRoutes();
        }

        if (config('system.routes.admin')) {
            self::setupAdminRoutes();
        }

        if (config('system.routes.user_api')) {
            self::setupUserApiRoutes();
        }

        if (config('system.routes.web')) {
            Route::middleware(['web', 'maintenace'])->group(base_path('routes/web.php'));
        }
    }

    /**
     * Setup user API routes
     * @return void
     */
    private static function setupUserApiRoutes()
    {
        
        if (!generalSetting('user_api')) return;
    
        Route::middleware(['api'])
            ->prefix('api/' . trim(generalSetting('user_prefix') ?? 'user', '/'))
            ->namespace(self::$controllerNamespace . 'Api\User')
            ->name('api.user.')
            ->group(base_path('routes/api/user.php'));
    }
}

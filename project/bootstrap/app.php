<?php

use App\Helpers\RoutesHelper;
use App\Http\Middleware\DemoMiddleware;
use App\Http\Middleware\KycMiddleware;
use App\Http\Middleware\MaintenanceMode;
use App\Http\Middleware\ProfileCompleteMiddleware;
use App\Http\Middleware\RedirectIfAdmin;
use App\Http\Middleware\RedirectIfNotAdmin;
use App\Http\Middleware\SiteVisitMiddleware;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: fn() => RoutesHelper::setupRoutes()
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->group('web', [
            \App\Http\Middleware\CheckInstallation::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\SetLocale::class,
            DemoMiddleware::class,
            \App\Http\Middleware\CheckLicense::class,
        ]);

        $middleware->alias([
            // 'demo'             => DemoMiddleware::class,
            'auth'             => Authenticate::class,
            'admin.guest'      => RedirectIfAdmin::class,
            'kyc'              => KycMiddleware::class,
            'admin'            => RedirectIfNotAdmin::class,
            'maintenace'       => MaintenanceMode::class,
            'profile.complete' => ProfileCompleteMiddleware::class,
            'count_site_visit' => SiteVisitMiddleware::class,
            'verified.custom'  => \App\Http\Middleware\EnsurePhoneOrEmailIsVerified::class,
        ]);

        $middleware->validateCsrfTokens(except: [
            'payment.notify',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(
            fn(Request $request, Throwable $e) =>
            $request->is('api/*') || $request->expectsJson()
        ); // :contentReference[oaicite:0]{index=0}

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(
                    [
                        'stauts'  => 'error',
                        'message' => 'Not found'
                    ],
                    Response::HTTP_NOT_FOUND
                );
            }
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(
                    [
                        'errors'  => $e->errors(),
                        'note'    => 'validation_error',
                        'stauts'  => 'error',
                        'message' => $e->getMessage()
                    ],
                    Response::HTTP_UNPROCESSABLE_ENTITY
                );
            }
        });

        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(
                    ['message' => 'Method not allowed.'],
                    Response::HTTP_METHOD_NOT_ALLOWED
                );
            }
        });

        $exceptions->render(function (UnauthorizedException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(
                    ['message' => 'Method not allowed.'],
                    Response::HTTP_METHOD_NOT_ALLOWED
                );
            }
        });
    })->create();

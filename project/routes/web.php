<?php

use App\Http\Controllers\CronJobController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\User\Auth\ResetPasswordController;
use App\Http\Controllers\User\Auth\SocialAuthController;
use App\Http\Controllers\User\Auth\TwoFactorController;
use App\Http\Controllers\User\ForgotPasswordController;
use App\Http\Controllers\User\PaymentController;
use App\Http\Middleware\MaintenanceMode;
use App\Services\FileService;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

Route::get('test', function() {
    sendTemplatedNotification(
        'monirsaikat@gmail.com',
        'PASSWORD_RESET_REQUEST',
        [
            'app_name' => 'ok ok bhaia',
            'user_name' => 'Saikat Monir',
        ]
    );
});

// Route::get('/installer/{any?}', function ($any = null) {
//     $path = base_path('installer/' . ($any ?? 'index.php'));
//     if (file_exists($path)) {
//         return response()->file($path);
//     }
//     abort(404);
// })->where('any', '.*');

// Route::get('ok', function() {
//     $u = User::where('email', 'jepotulozi@mailinator.com')->first();
//     $u->password = Hash::make('jepotulozi@mailinator.com');
//     $u->save();
// });

Route::get('cron-job', [CronJobController::class, 'index'])->name('cronjob');

Route::get('/two-factor-challenge', [TwoFactorController::class, 'challenge'])->name('2fa.challenge');
Route::post('/two-factor-challenge', [TwoFactorController::class, 'verify'])->name('2fa.verify');

// Route::get('roles', function () {
//     $admin  = Admin::first();

//     // Create a 'super-admin' role if it doesn't exist
//     Bouncer::allow('super-admin')->everything();

//     // Assign the 'super-admin' role to this admin
//     Bouncer::assign('super-admin')->to($admin);

//     // Optionally, create specific abilities
//     Bouncer::allow($admin)->to('manage-users');
//     Bouncer::allow($admin)->to('view-dashboard');
// });

Route::get('auth/{provider}', [SocialAuthController::class, 'redirect'])->name('social.redirect');
Route::get('auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');

Route::get('/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('storage:link');

    return '<h3>✅ Cache cleared, config/routes/views reset, and storage linked successfully.</h3>';
})->name('system.clear')->withoutMiddleware(MaintenanceMode::class);

Route::middleware('count_site_visit')->group(function () {
    Route::get('/email/verify', function () {
        if(auth()->user()->email_verified_at) {
            return to_route('user.dashboard');
        }
        
        return theme('user.auth.verify-email');
    })->middleware('auth')->name('verification.notice');

    Route::get('/phone/verify', function () {
        return theme('user.auth.verify-phone');
    })->middleware('auth')->name('user.phone.verify.notice');

    // Route::post('/phone/verify', [\App\Http\Controllers\User\Auth\PhoneVerificationController::class, 'verify'])
    //     ->middleware('auth')
    //     ->name('user.phone.verify.submit');

    // Route::post('/phone/resend', [\App\Http\Controllers\User\Auth\PhoneVerificationController::class, 'resend'])
    //     ->middleware('auth')
    //     ->name('user.phone.verify.resend');


    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return to_route('user.dashboard')->withSuccess(__('Email verified successfully'));
    })->middleware(['auth', 'signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'Verification link sent!');
    })->middleware(['auth', 'throttle:6,1'])->name('verification.send');

    Route::controller(\App\Http\Controllers\User\Auth\RegisterController::class)->middleware('guest')->group(function () {
        Route::get('/register', 'register')->name('register');
        Route::post('/register', 'registerStore')->name('register.store');
    });

    Route::controller(\App\Http\Controllers\User\LoginController::class)->group(function () {
        Route::middleware('guest')->group(function () {
            Route::get('/login', 'login')->name('login');
            Route::post('/login', 'loginStore')->name('login.store');
            Route::post('/otp-login', 'otpLoginStore')->name('login.otp.store');
            Route::post('/otp-login-check', 'validateOtpAndLogin')->name('login.otp.validate');
        });

        Route::middleware('auth')->group(function () {
            Route::post('/logout', 'logout')->name('logout');
        });
    });

    Route::middleware('guest')->group(function () {
        Route::get('/forgot-password', [ForgotPasswordController::class, 'requestForm'])->name('password.request');
        Route::post('/forgot-password', [ForgotPasswordController::class, 'sendLink'])->name('password.email');

        Route::get('/reset-password/{token}', [ResetPasswordController::class, 'resetForm'])->name('password.reset');
        Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword'])->name('password.update');
    });

    Route::get('/', [SiteController::class, 'home'])->name('home');

    Route::get('/protected-files/{filename}', function ($filename, FileService $fileService) {
        abort_unless(auth()->check(), 403);
        return $fileService->download($filename);
    })->name('files.protected');

    Route::any('payment/notify/{key}', [PaymentController::class, 'notify'])->name('payment.notify')->withoutMiddleware(VerifyCsrfToken::class);

    Route::get('/payment/success', [PaymentController::class, 'paymentSuccess'])->name('payment.success');

    Route::get('/change-lang/{lang}', [SiteController::class, 'changeLang'])->name('lang.switch')->withoutMiddleware(MaintenanceMode::class);

    Route::get('/blog', [SiteController::class, 'blogs'])->name('site.blogs');
    Route::get('/blog/{slug}', [SiteController::class, 'blogDetails'])->name('site.blog.details');
    Route::get('/contact', [SiteController::class, 'contact'])->name('site.contact');
    Route::get('/{pageSlug}', [SiteController::class, 'renderPageBySlug'])->name('site.page');
});

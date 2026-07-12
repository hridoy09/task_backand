<?php

use App\Helpers\RoutesHelper;
use Illuminate\Support\Facades\Route;

RoutesHelper::registerUserRoutes(function () {
    Route::controller('UserController')->middleware('verified')->group(function () {
        Route::get('/profile/complete', 'profileComplete')->name('profile_data');
        Route::post('/profile/complete/save', 'saveCompleteProfile')->name('save_profile_data');
    });

    Route::controller('Auth\TwoFactorController')->group(function() {
        Route::get('/settings/2fa', 'show')->name('2fa.settings');
        Route::post('/settings/2fa/start', 'start')->name('2fa.start');
        Route::post('/settings/2fa/confirm', 'confirm')->name('2fa.confirm');
        Route::delete('/settings/2fa', 'disable')->name('2fa.disable');
        Route::post('/settings/2fa/recovery-codes', 'regenerateCodes')->name('2fa.recovery.regen');
    });

    Route::controller('KycController')->name('kyc.')->group(function() {
        Route::get('/kyc', 'kycForm')->name('form');
        Route::post('/kyc', 'kycSubmit')->name('submit');
    });

    Route::middleware(['verified.custom', 'profile.complete'])->group(function () {
        Route::controller('SupportTicketController')->name('support.')->prefix('support-tickets')->group(function() {
            Route::get('/', 'list')->name('list');
            Route::get('/open-ticket', 'openTicket')->name('open');
            Route::post('/open-ticket', 'store')->name('store');
        }); 
        
        Route::controller('UserController')->middleware('verified')->group(function () {
            Route::get('/dashboard', 'dashboard')->name('dashboard');
            Route::get('/setting/profile', 'profile')->name('setting.profile');
            Route::post('/setting/profile', 'saveProfile')->name('setting.profile.save');
        });

        Route::middleware('kyc')->controller('PaymentController')->prefix('payment')->name('payment.')->group(function () {
            Route::get('/history', 'paymentHistory')->name('history');
            Route::get('/new', 'newPayment')->name('new');
            Route::post('/', 'paymentInsert')->name('insert');
        });
    });
});

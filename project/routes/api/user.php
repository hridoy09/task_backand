<?php

use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::controller('Auth\LoginController')->group(function () {
        Route::post('/login', 'login')->name('login');
    });

    Route::controller('Auth\RegisterController')->group(function () {
        Route::post('/register', 'register')->name('register');
    });
});

Route::middleware('auth:sanctum')->group(function () {
Route::post('/complete-profile', 'ProfileCompleteController');

    Route::middleware('profile.complete')->group(function () {
        Route::controller('UserController')->group(function () {
            Route::get('/dashboard', 'dashboard')->name('dashboard');
        });
    });
});

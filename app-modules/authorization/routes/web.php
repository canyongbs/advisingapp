<?php

use Assist\Authorization\Http\Controllers\SocialiteController;
use Assist\Authorization\Http\Controllers\Auth\LogoutController;
use Assist\Authorization\Http\Controllers\Auth\OneTimeLoginController;

Route::middleware('web')->group(function () {
    Route::prefix('auth')->name('socialite.')->group(function () {
        Route::get('/{provider}/redirect', [SocialiteController::class, 'redirect'])
            ->name('redirect');

        Route::get('/{provider}/callback', [SocialiteController::class, 'callback'])
            ->name('callback');
    });

    Route::get('/auth/login/{user}', OneTimeLoginController::class)
        ->name('login.one-time')
        ->middleware('signed');

    Route::post('/auth/logout', LogoutController::class)
        ->name('logout');
});

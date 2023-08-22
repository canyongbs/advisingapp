<?php

use Assist\Authorization\Http\Controllers\SocialiteController;
use Assist\Authorization\Http\Controllers\auth\LogoutController;

Route::middleware('web')->prefix('auth')->name('socialite.')->group(function () {
    Route::get('/{provider}/redirect', [SocialiteController::class, 'redirect'])->name('redirect');
    Route::get('/{provider}/callback', [SocialiteController::class, 'callback'])->name('callback');
});

Route::middleware('web')->post('/auth/logout', LogoutController::class)->name('logout');

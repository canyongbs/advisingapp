<?php

use Assist\MeetingCenter\Http\Controllers\GoogleCalendarLoginController;
use Assist\MeetingCenter\Http\Controllers\GoogleCalendarRedirectController;

Route::middleware(['web', 'auth'])
    ->name('google.calendar.')
    ->group(function () {
        Route::get('/google/calendar/login', GoogleCalendarLoginController::class)->name('login');

        Route::get(config('services.google_calendar.redirect'), GoogleCalendarRedirectController::class)->name('callback');
    });

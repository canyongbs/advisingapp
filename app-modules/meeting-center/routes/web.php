<?php

use App\Models\User;
use Assist\MeetingCenter\Http\Controllers\GoogleCalendarRedirectController;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Assist\MeetingCenter\Models\Calendar;
use League\OAuth2\Client\Provider\Google;
use League\OAuth2\Client\Grant\RefreshToken;
use Google\Service\Calendar as GoogleCalendar;
use Symfony\Component\HttpFoundation\Response;
use Assist\MeetingCenter\GoogleCalendarManager;
use Assist\MeetingCenter\Http\Controllers\GoogleCalendarLoginController;

Route::middleware(['web', 'auth'])
    ->name('google.calendar.')
    ->group(function () {
        Route::get('/google/calendar/login', GoogleCalendarLoginController::class)->name('login');

        Route::get(config('services.google_calendar.redirect'), GoogleCalendarRedirectController::class)->name('callback');
    });

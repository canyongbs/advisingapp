<?php

use Assist\MeetingCenter\Enums\CalendarProvider;
use Assist\MeetingCenter\Http\Controllers\GoogleCalendarController;
use Assist\MeetingCenter\Http\Controllers\OutlookCalendarController;

Route::middleware(['web', 'auth'])
    ->name('calendar.')
    ->prefix('/calendar')
    ->group(function () {
        Route::name(CalendarProvider::Google->value . '.')
            ->prefix('/' . CalendarProvider::Google->value)
            ->controller(GoogleCalendarController::class)
            ->group(function () {
                Route::get('/login', 'login')->name('login');
                Route::get('/callback', 'callback')->name('callback');
            });

        Route::name(CalendarProvider::Outlook->value . '.')
            ->prefix('/' . CalendarProvider::Outlook->value)
            ->controller(OutlookCalendarController::class)
            ->group(function () {
                Route::get('/login', 'login')->name('login');
                Route::get('/callback', 'callback')->name('callback');
            });
    });

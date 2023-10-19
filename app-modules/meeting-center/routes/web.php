<?php

use Assist\MeetingCenter\Enums\CalendarProvider;
use Assist\MeetingCenter\Http\Controllers\GoogleCalendarController;
use Assist\MeetingCenter\Http\Controllers\OutlookCalendarController;

Route::middleware(['web', 'auth'])
    ->name('calendar.')
    ->prefix('/calendar')
    ->group(function () {
        provider_routes(CalendarProvider::Google, GoogleCalendarController::class);
        provider_routes(CalendarProvider::Outlook, OutlookCalendarController::class);
    });

<?php

use Illuminate\Routing\RouteRegistrar;
use Assist\MeetingCenter\Enums\CalendarProvider;
use Assist\MeetingCenter\Http\Controllers\GoogleCalendarController;
use Assist\MeetingCenter\Http\Controllers\OutlookCalendarController;

Route::middleware(['web', 'auth'])
    ->name('calendar.')
    ->prefix('/calendar')
    ->group(function () {
        providerRoutes(CalendarProvider::Google, GoogleCalendarController::class);
        providerRoutes(CalendarProvider::Outlook, OutlookCalendarController::class);
    });

function providerRoutes(CalendarProvider $provider, string $controller): RouteRegistrar
{
    return Route::name($provider->value . '.')
        ->prefix('/' . $provider->value)
        ->controller($controller)
        ->group(function () {
            Route::get('/login', 'login')->name('login');
            Route::get('/callback', 'callback')->name('callback');
        });
}

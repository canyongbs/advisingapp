<?php

use Illuminate\Routing\RouteRegistrar;
use Assist\MeetingCenter\Enums\CalendarProvider;

if (! function_exists('provider_routes')) {
    function provider_routes(CalendarProvider $provider, string $controller): RouteRegistrar
    {
        return Route::name($provider->value . '.')
            ->prefix('/' . $provider->value)
            ->controller($controller)
            ->group(function () {
                Route::get('/login', 'login')->name('login');
                Route::get('/callback', 'callback')->name('callback');
            });
    }
}

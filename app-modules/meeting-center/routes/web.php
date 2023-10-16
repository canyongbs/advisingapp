<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Assist\MeetingCenter\Models\Calendar;
use League\OAuth2\Client\Provider\Google;
use League\OAuth2\Client\Grant\RefreshToken;
use Google\Service\Calendar as GoogleCalendar;
use Symfony\Component\HttpFoundation\Response;
use League\OAuth2\Client\Grant\AuthorizationCode;

Route::middleware(['web', 'auth'])
    ->prefix('google/calendar')
    ->name('google.calendar.')
    ->group(function () {
        Route::get('/login', function () {
            $provider = new Google([
                'clientId' => config('services.google_calendar.client_id'),
                'clientSecret' => config('services.google_calendar.client_secret'),
                'redirectUri' => route('google.calendar.callback'),
                'scopes' => [GoogleCalendar::CALENDAR, GoogleCalendar::CALENDAR_EVENTS],
                'prompt' => 'consent',
                'accessType' => 'offline',
            ]);

            return redirect()->away($provider->getAuthorizationUrl());
        })->name('login');

        Route::get('/callback', function (Request $request) {
            $provider = new Google([
                'clientId' => config('services.google_calendar.client_id'),
                'clientSecret' => config('services.google_calendar.client_secret'),
                'redirectUri' => route('google.calendar.callback'),
                'scopes' => [GoogleCalendar::CALENDAR, GoogleCalendar::CALENDAR_EVENTS],
                'prompt' => 'consent',
                'accessType' => 'offline',
            ]);

            $token = $provider->getAccessToken(new AuthorizationCode(), [
                'code' => $request->input('code'),
            ]);

            /** @var User $user */
            $user = auth()->user();

            $calendar = new Calendar();
            $calendar->type = 'google';
            $calendar->provider_id = env('GOOGLE_CALENDAR_ID'); //TODO: needs UI to select calendar
            $calendar->oauth_token = $token->getToken();
            $calendar->oauth_refresh_token = $token->getRefreshToken();
            $calendar->oauth_token_expires_at = Carbon::parse($token->getExpires());

            $user->calendar()->save($calendar);
        })->name('callback');

        Route::get('/refresh', function (Request $request) {
            /** @var User $user */
            $user = auth()->user();

            // abort_unless($user->calendar_type === 'google', Response::HTTP_BAD_REQUEST);
            //
            // if (blank($user->calendar_id)) {
            //     redirect()->route('google.calendar.login');
            // }

            $calendar = $user->calendar;

            $provider = new Google([
                'clientId' => config('services.google_calendar.client_id'),
                'clientSecret' => config('services.google_calendar.client_secret'),
                'redirectUri' => route('google.calendar.callback'),
                'scopes' => [GoogleCalendar::CALENDAR, GoogleCalendar::CALENDAR_EVENTS],
                'prompt' => 'consent',
                'accessType' => 'offline',
            ]);

            $token = $provider->getAccessToken(new RefreshToken(), [
                'refresh_token' => $calendar->oauth_refresh_token,
            ]);

            $calendar->oauth_token = $token->getToken();
            $calendar->oauth_token_expires_at = Carbon::parse($token->getExpires());
            $calendar->save();
        })->name('refresh');
    });

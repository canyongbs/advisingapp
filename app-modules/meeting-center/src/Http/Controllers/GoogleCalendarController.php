<?php

namespace Assist\MeetingCenter\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Filament\Facades\Filament;
use Illuminate\Support\Carbon;
use Illuminate\Http\RedirectResponse;
use Assist\MeetingCenter\Models\Calendar;
use Assist\MeetingCenter\Enums\CalendarProvider;
use Assist\MeetingCenter\Managers\GoogleCalendarManager;

class GoogleCalendarController extends CalendarController
{
    public function login(Request $request): RedirectResponse
    {
        $client = GoogleCalendarManager::client();

        return redirect()->away($client->createAuthUrl());
    }

    public function callback(Request $request): RedirectResponse
    {
        $client = GoogleCalendarManager::client();

        $token = $client->fetchAccessTokenWithAuthCode($request->get('code'));

        /** @var User $user */
        $user = auth()->user();

        $calendar = $user->calendar ?: new Calendar();
        $calendar->type = CalendarProvider::Google;
        $calendar->oauth_token = $token['access_token'];
        $calendar->oauth_refresh_token = $token['refresh_token'];
        $calendar->oauth_token_expires_at = Carbon::parse($token['created'] + $token['expires_in']);

        $user->calendar()->save($calendar);

        return redirect()->to(Filament::getUrl());
    }
}

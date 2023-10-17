<?php

namespace Assist\MeetingCenter\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Assist\MeetingCenter\Models\Calendar;
use Assist\MeetingCenter\GoogleCalendarManager;

class GoogleCalendarRedirectController extends Controller
{
    public function __invoke(Request $request): void
    {
        $client = GoogleCalendarManager::client();

        $token = $client->fetchAccessTokenWithAuthCode($request->get('code'));

        /** @var User $user */
        $user = auth()->user();

        $calendar = $user->calendar ?: new Calendar();
        $calendar->type = GoogleCalendarManager::type();
        $calendar->provider_id = env('GOOGLE_CALENDAR_ID'); //TODO: needs UI to select calendar
        $calendar->oauth_token = $token['access_token'];
        $calendar->oauth_refresh_token = $token['refresh_token'];
        $calendar->oauth_token_expires_at = Carbon::parse($token['created'] + $token['expires_in']);

        $user->calendar()->save($calendar);
    }
}

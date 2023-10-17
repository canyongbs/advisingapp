<?php

namespace Assist\MeetingCenter\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Assist\MeetingCenter\GoogleCalendarManager;

class GoogleCalendarLoginController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $client = GoogleCalendarManager::client();

        return redirect()->away($client->createAuthUrl());
    }
}

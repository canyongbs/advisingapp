<?php

namespace Assist\MeetingCenter\Http\Controllers;

use App\Http\Controllers\Controller;
use Assist\MeetingCenter\GoogleCalendarManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GoogleCalendarLoginController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $client = GoogleCalendarManager::client();

        return redirect()->away($client->createAuthUrl());
    }
}

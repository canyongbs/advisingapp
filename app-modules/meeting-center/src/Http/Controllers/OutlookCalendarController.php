<?php

namespace Assist\MeetingCenter\Http\Controllers;

use Illuminate\Http\Request;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;

class OutlookCalendarController extends CalendarController
{
    public function login(Request $request): RedirectResponse
    {
        return redirect()->to('https://outlook.com/');
    }

    public function callback(Request $request): RedirectResponse
    {
        return redirect()->to(Filament::getUrl());
    }
}

<?php

namespace Assist\MeetingCenter\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

abstract class CalendarController extends Controller
{
    abstract public function login(Request $request): RedirectResponse;

    abstract public function callback(Request $request): RedirectResponse;
}

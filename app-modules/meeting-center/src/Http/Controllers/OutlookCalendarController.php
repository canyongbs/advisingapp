<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\MeetingCenter\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Two\User;
use Illuminate\Http\RedirectResponse;
use SocialiteProviders\Azure\Provider;
use Assist\MeetingCenter\Models\Calendar;
use Assist\MeetingCenter\Enums\CalendarProvider;
use Assist\Authorization\Enums\SocialiteProvider;
use Assist\MeetingCenter\Filament\Resources\CalendarEventResource\Pages\ListCalendarEvents;

class OutlookCalendarController extends CalendarController
{
    public function login(Request $request): RedirectResponse
    {
        /** @var Provider $driver */
        $driver = SocialiteProvider::AzureCalendar->driver();

        return $driver
            ->setConfig(SocialiteProvider::AzureCalendar->config())
            ->scopes(['Calendars.ReadWrite', 'User.Read', 'offline_access'])
            ->redirect();
    }

    public function callback(Request $request): RedirectResponse
    {
        /** @var Provider $driver */
        $driver = SocialiteProvider::AzureCalendar->driver();

        /** @var User $socialiteUser */
        $socialiteUser = $driver
            ->setConfig(SocialiteProvider::AzureCalendar->config())
            ->user();

        $user = auth()->user();

        $calendar = $user->calendar ?: new Calendar();

        $calendar->provider_type = CalendarProvider::Outlook;
        $calendar->provider_email = $socialiteUser->getEmail();
        $calendar->oauth_token = $socialiteUser->token;
        $calendar->oauth_refresh_token = $socialiteUser->refreshToken;
        $calendar->oauth_token_expires_at = now()->addSeconds($socialiteUser->expiresIn);

        $user->calendar()->save($calendar);

        return redirect()->to(ListCalendarEvents::getUrl());
    }
}

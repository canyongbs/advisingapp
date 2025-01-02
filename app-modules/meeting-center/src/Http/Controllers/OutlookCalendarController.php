<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\MeetingCenter\Http\Controllers;

use AdvisingApp\Authorization\Enums\SocialiteProvider;
use AdvisingApp\MeetingCenter\Enums\CalendarProvider;
use AdvisingApp\MeetingCenter\Filament\Resources\CalendarEventResource\Pages\ListCalendarEvents;
use AdvisingApp\MeetingCenter\Models\Calendar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Two\User;
use SocialiteProviders\Azure\Provider;

class OutlookCalendarController extends CalendarController
{
    public function login(Request $request): RedirectResponse
    {
        /** @var Provider $driver */
        $driver = SocialiteProvider::AzureCalendar->driver();

        return $driver
            ->setConfig(SocialiteProvider::AzureCalendar->config())
            ->scopes(['Calendars.ReadWrite', 'User.Read', 'offline_access'])
            ->with(['prompt' => 'select_account'])
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

<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\MeetingCenter\Filament\Resources\CalendarEvents\CalendarEventResource;
use AdvisingApp\MeetingCenter\Filament\Resources\CalendarEvents\Pages\CreateCalendarEvent;
use AdvisingApp\MeetingCenter\Models\Calendar;
use App\Models\User;
use App\Settings\LicenseSettings;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

$createCalendarEventTestUser = function (): User {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->scheduleAndAppointments = true;
    $settings->save();

    $user = User::factory()->licensed(LicenseType::cases())->create();
    $user->givePermissionTo('calendar_event.view-any', 'calendar_event.create');

    return $user;
};

it('can render the create calendar event page', function () use ($createCalendarEventTestUser): void {
    $user = $createCalendarEventTestUser();

    Calendar::factory()->for($user)->create();

    actingAs($user);

    get(CalendarEventResource::getUrl('create'))->assertSuccessful();
});

it('defaults the attendees to the email of the connected calendar', function () use ($createCalendarEventTestUser): void {
    $user = $createCalendarEventTestUser();

    $calendar = Calendar::factory()->for($user)->create();

    actingAs($user);

    Livewire::test(CreateCalendarEvent::class)
        ->assertSchemaStateSet(['attendees' => [$calendar->provider_email]]);
});

it('redirects to the schedule to set up a calendar when one is not set up', function (?array $calendarState) use ($createCalendarEventTestUser): void {
    $user = $createCalendarEventTestUser();

    if (! is_null($calendarState)) {
        Calendar::factory()->for($user)->create($calendarState);
    }

    actingAs($user);

    get(CalendarEventResource::getUrl('create'))
        ->assertRedirect(CalendarEventResource::getUrl('index'));
})->with([
    'no calendar' => [null],
    'calendar not connected' => [['oauth_token' => null]],
    'calendar not selected' => [['provider_id' => null]],
]);

describe('authorization', function () use ($createCalendarEventTestUser): void {
    it('denies access to the create page without the `calendar_event.create` permission', function () use ($createCalendarEventTestUser): void {
        $user = $createCalendarEventTestUser();
        $user->revokePermissionTo('calendar_event.create');

        actingAs($user);

        get(CalendarEventResource::getUrl('create'))->assertForbidden();
    });
});

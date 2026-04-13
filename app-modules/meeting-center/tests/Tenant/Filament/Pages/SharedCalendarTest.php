<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
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

use AdvisingApp\MeetingCenter\Filament\Pages\SharedCalendar;
use AdvisingApp\MeetingCenter\Models\BookingGroup;
use AdvisingApp\MeetingCenter\Models\BookingGroupAppointment;
use AdvisingApp\Team\Models\Team;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('`SharedCalendar` page can be rendered', function () {
    asSuperAdmin();

    get(SharedCalendar::getUrl())
        ->assertSuccessful();
});

test('`SharedCalendar` is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(SharedCalendar::getUrl())
        ->assertForbidden();

    $user->givePermissionTo('group_appointment.view-any');

    actingAs($user)
        ->get(SharedCalendar::getUrl())
        ->assertSuccessful();
});

test('`SharedCalendar` page has the correct table columns', function () {
    asSuperAdmin();

    livewire(SharedCalendar::class)
        ->assertSuccessful()
        ->assertTableColumnExists('name')
        ->assertTableColumnExists('email')
        ->assertTableColumnExists('bookingGroup.name')
        ->assertTableColumnExists('starts_at')
        ->assertTableColumnExists('ends_at')
        ->assertTableColumnExists('duration');
});

test('`SharedCalendar` table defaults to hiding past appointments', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('group_appointment.view-any');
    actingAs($user);

    $group = BookingGroup::factory()->create();
    $group->users()->attach($user->id);

    $futureAppointment = BookingGroupAppointment::factory()
        ->for($group, 'bookingGroup')
        ->create(['starts_at' => now()->addDay(), 'ends_at' => now()->addDay()->addHour()]);

    $pastAppointment = BookingGroupAppointment::factory()
        ->past()
        ->for($group, 'bookingGroup')
        ->create();

    livewire(SharedCalendar::class)
        ->assertSuccessful()
        ->assertCountTableRecords(1)
        ->assertCanSeeTableRecords([$futureAppointment])
        ->assertCanNotSeeTableRecords([$pastAppointment]);
});

test('`SharedCalendar` table shows past appointments when Hide Past is disabled', function () {
    $user = User::factory()->create();
    $user->givePermissionTo('group_appointment.view-any');
    actingAs($user);

    $group = BookingGroup::factory()->create();
    $group->users()->attach($user->id);

    $futureAppointment = BookingGroupAppointment::factory()
        ->for($group, 'bookingGroup')
        ->create(['starts_at' => now()->addDay(), 'ends_at' => now()->addDay()->addHour()]);

    $pastAppointment = BookingGroupAppointment::factory()
        ->past()
        ->for($group, 'bookingGroup')
        ->create();

    livewire(SharedCalendar::class)
        ->set('data', [
            'groupFilter' => 'my_groups',
            'selectedGroupIds' => [],
            'hidePast' => false,
        ])
        ->assertCountTableRecords(2)
        ->assertCanSeeTableRecords([$futureAppointment, $pastAppointment]);
});

test("`SharedCalendar` 'My Groups' filter only shows appointments from the user's directly-assigned groups", function () {
    $user = User::factory()->create();
    $user->givePermissionTo('group_appointment.view-any');
    actingAs($user);

    $myGroup = BookingGroup::factory()->create();
    $myGroup->users()->attach($user->id);

    $otherGroup = BookingGroup::factory()->create();

    $myAppointment = BookingGroupAppointment::factory()
        ->for($myGroup, 'bookingGroup')
        ->create(['starts_at' => now()->addDay(), 'ends_at' => now()->addDay()->addHour()]);

    $otherAppointment = BookingGroupAppointment::factory()
        ->for($otherGroup, 'bookingGroup')
        ->create(['starts_at' => now()->addDay(), 'ends_at' => now()->addDay()->addHour()]);

    livewire(SharedCalendar::class)
        ->set('data', [
            'groupFilter' => 'my_groups',
            'selectedGroupIds' => [],
            'hidePast' => false,
        ])
        ->assertCountTableRecords(1)
        ->assertCanSeeTableRecords([$myAppointment])
        ->assertCanNotSeeTableRecords([$otherAppointment]);
});

test("`SharedCalendar` 'My Groups' filter includes appointments from groups linked to the user's team", function () {
    $team = Team::factory()->create();
    $user = User::factory()->create(['team_id' => $team->id]);
    $user->givePermissionTo('group_appointment.view-any');
    actingAs($user);

    $teamGroup = BookingGroup::factory()->create();
    $teamGroup->teams()->attach($team->id);

    $otherGroup = BookingGroup::factory()->create();

    $teamAppointment = BookingGroupAppointment::factory()
        ->for($teamGroup, 'bookingGroup')
        ->create(['starts_at' => now()->addDay(), 'ends_at' => now()->addDay()->addHour()]);

    $otherAppointment = BookingGroupAppointment::factory()
        ->for($otherGroup, 'bookingGroup')
        ->create(['starts_at' => now()->addDay(), 'ends_at' => now()->addDay()->addHour()]);

    livewire(SharedCalendar::class)
        ->set('data', [
            'groupFilter' => 'my_groups',
            'selectedGroupIds' => [],
            'hidePast' => false,
        ])
        ->assertCountTableRecords(1)
        ->assertCanSeeTableRecords([$teamAppointment])
        ->assertCanNotSeeTableRecords([$otherAppointment]);
});

test("`SharedCalendar` 'Selected Groups' filter shows appointments from the specified groups only", function () {
    $user = User::factory()->create();
    $user->givePermissionTo('group_appointment.view-any');
    actingAs($user);

    $groupA = BookingGroup::factory()->create();
    $groupB = BookingGroup::factory()->create();
    $groupC = BookingGroup::factory()->create();

    $appointmentA = BookingGroupAppointment::factory()
        ->for($groupA, 'bookingGroup')
        ->create(['starts_at' => now()->addDay(), 'ends_at' => now()->addDay()->addHour()]);

    $appointmentB = BookingGroupAppointment::factory()
        ->for($groupB, 'bookingGroup')
        ->create(['starts_at' => now()->addDay(), 'ends_at' => now()->addDay()->addHour()]);

    $appointmentC = BookingGroupAppointment::factory()
        ->for($groupC, 'bookingGroup')
        ->create(['starts_at' => now()->addDay(), 'ends_at' => now()->addDay()->addHour()]);

    livewire(SharedCalendar::class)
        ->set('data', [
            'groupFilter' => 'selected',
            'selectedGroupIds' => [$groupA->id, $groupB->id],
            'hidePast' => false,
        ])
        ->assertCountTableRecords(2)
        ->assertCanSeeTableRecords([$appointmentA, $appointmentB])
        ->assertCanNotSeeTableRecords([$appointmentC]);
});

test("`SharedCalendar` 'Selected Groups' filter with no groups selected shows all appointments", function () {
    $group = BookingGroup::factory()->create();

    BookingGroupAppointment::factory()
        ->for($group, 'bookingGroup')
        ->create(['starts_at' => now()->addDay(), 'ends_at' => now()->addDay()->addHour()]);

    asSuperAdmin();

    livewire(SharedCalendar::class)
        ->set('data', [
            'groupFilter' => 'selected',
            'selectedGroupIds' => [],
            'hidePast' => false,
        ])
        ->assertCountTableRecords(1);
});

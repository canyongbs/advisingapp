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

use AdvisingApp\MeetingCenter\Filament\Resources\BookingGroups\Pages\ViewBookingGroup;
use AdvisingApp\MeetingCenter\Models\BookingGroup;
use AdvisingApp\Team\Models\Team;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;

it('can render with proper permission', function () {
    $user = User::factory()->create();
    actingAs($user);

    $bookingGroup = BookingGroup::factory()->create();

    get(ViewBookingGroup::getUrl([
        'record' => $bookingGroup->getRouteKey(),
    ]))
        ->assertForbidden();

    $user->givePermissionTo('group_appointment.view-any');
    $user->givePermissionTo('group_appointment.*.view');

    $user->refresh();

    actingAs($user);

    $bookingGroup = BookingGroup::factory()
        ->for($user, 'createdBy')
        ->create();

    get(ViewBookingGroup::getUrl([
        'record' => $bookingGroup->getRouteKey(),
    ]))
        ->assertSuccessful();
});

it('displays booking group basic information', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('group_appointment.view-any');
    $user->givePermissionTo('group_appointment.*.view');

    actingAs($user);

    $bookingGroup = BookingGroup::factory()->create([
        'name' => 'Test Booking Group',
        'description' => 'This is a test description',
        'is_confidential' => false,
    ]);

    livewire(ViewBookingGroup::class, [
        'record' => $bookingGroup->getRouteKey(),
    ])
        ->assertSee('Test Booking Group')
        ->assertSee('This is a test description')
        ->assertHasNoErrors();
});

it('displays users for confidential booking groups', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('group_appointment.view-any');
    $user->givePermissionTo('group_appointment.*.view');

    actingAs($user);

    $users = User::factory()->count(3)->create();

    $bookingGroup = BookingGroup::factory()->create([
        'is_confidential' => true,
    ]);

    $bookingGroup->users()->attach($users);

    livewire(ViewBookingGroup::class, [
        'record' => $bookingGroup->getRouteKey(),
    ])
        ->assertSee($users[0]->name)
        ->assertSee($users[1]->name)
        ->assertSee($users[2]->name)
        ->assertHasNoErrors();
});

it('displays teams for confidential booking groups', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('group_appointment.view-any');
    $user->givePermissionTo('group_appointment.*.view');

    actingAs($user);

    $teams = Team::factory()->count(2)->create();

    $bookingGroup = BookingGroup::factory()->create([
        'is_confidential' => true,
    ]);

    $bookingGroup->teams()->attach($teams);

    livewire(ViewBookingGroup::class, [
        'record' => $bookingGroup->getRouteKey(),
    ])
        ->assertSee($teams[0]->name)
        ->assertSee($teams[1]->name)
        ->assertHasNoErrors();
});

it('displays N/A for confidential booking groups without users', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('group_appointment.view-any');
    $user->givePermissionTo('group_appointment.*.view');

    actingAs($user);

    $bookingGroup = BookingGroup::factory()->create([
        'is_confidential' => true,
    ]);

    livewire(ViewBookingGroup::class, [
        'record' => $bookingGroup->getRouteKey(),
    ])
        ->assertSee('N/A')
        ->assertHasNoErrors();
});

it('displays booking group with both users and teams', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('group_appointment.view-any');
    $user->givePermissionTo('group_appointment.*.view');

    actingAs($user);

    $users = User::factory()->count(2)->create();
    $teams = Team::factory()->count(2)->create();

    $bookingGroup = BookingGroup::factory()->create([
        'name' => 'Mixed Booking Group',
        'is_confidential' => true,
    ]);

    $bookingGroup->users()->attach($users);
    $bookingGroup->teams()->attach($teams);

    livewire(ViewBookingGroup::class, [
        'record' => $bookingGroup->getRouteKey(),
    ])
        ->assertSee('Mixed Booking Group')
        ->assertSee($users[0]->name)
        ->assertSee($users[1]->name)
        ->assertSee($teams[0]->name)
        ->assertSee($teams[1]->name)
        ->assertHasNoErrors();
});

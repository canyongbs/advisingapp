<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\MeetingCenter\Filament\Resources\BookingGroups\Pages\CreateBookingGroup;
use AdvisingApp\MeetingCenter\Models\BookingGroup;
use AdvisingApp\MeetingCenter\Tests\Tenant\Filament\Resources\BookingGroups\Pages\RequestFactory\CreateBookingGroupRequestFactory;
use AdvisingApp\Team\Models\Team;
use App\Models\User;
use Filament\Forms\Components\Repeater;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('can render with proper permission', function () {
    $user = User::factory()->create();

    actingAs($user);

    get(CreateBookingGroup::getUrl())
        ->assertForbidden();

    $user->givePermissionTo('group_appointment.view-any');
    $user->givePermissionTo('group_appointment.create');

    $user->refresh();

    actingAs($user);

    get(CreateBookingGroup::getUrl())
        ->assertSuccessful();
});

it('validates the inputs', function (CreateBookingGroupRequestFactory $data, array $errors) {
    asSuperAdmin();

    $request = CreateBookingGroupRequestFactory::new($data)->create();

    $user = User::factory()->create();

    livewire(CreateBookingGroup::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasFormErrors($errors);

    assertDatabaseCount(BookingGroup::class, 0);
})->with([
    'name required' => fn () => [
        CreateBookingGroupRequestFactory::new()->without('name'),
        ['name' => 'required'],
    ],
    'name string' => fn () => [
        CreateBookingGroupRequestFactory::new()->state(['name' => 1]),
        ['name' => 'string'],
    ],
    'name max' => fn () => [
        CreateBookingGroupRequestFactory::new()->state(['name' => str()->random(256)]),
        ['name' => 'max'],
    ],
    'slug required' => fn () => [
        CreateBookingGroupRequestFactory::new()->without('slug'),
        ['slug' => 'required'],
    ],
    'description string' => fn () => [
        CreateBookingGroupRequestFactory::new()->state(['description' => 1]),
        ['description' => 'string'],
    ],
    'description max' => fn () => [
        CreateBookingGroupRequestFactory::new()->state(['description' => str()->random(65536)]),
        ['description' => 'max'],
    ],
]);

it('can create a booking group with users and teams', function () {
    $undoRepeaterFake = Repeater::fake();

    $user = User::factory()->create();

    $user->givePermissionTo('group_appointment.view-any');
    $user->givePermissionTo('group_appointment.create');

    actingAs($user);

    $users = User::factory()->count(3)->create();
    $teams = Team::factory()->count(2)->create();

    $request = CreateBookingGroupRequestFactory::new()->state([
        'users' => $users->pluck('id')->toArray(),
        'teams' => $teams->pluck('id')->toArray(),
    ])->create();

    livewire(CreateBookingGroup::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertHasNoErrors();

    assertDatabaseCount(BookingGroup::class, 1);

    $bookingGroup = BookingGroup::first();
    assert($bookingGroup instanceof BookingGroup);

    expect($bookingGroup->users)->toHaveCount(3);
    expect($bookingGroup->teams)->toHaveCount(2);

    $undoRepeaterFake();
});

it('tracks created_by user correctly', function () {
    $undoRepeaterFake = Repeater::fake();

    $user = User::factory()->create();

    $user->givePermissionTo('group_appointment.view-any');
    $user->givePermissionTo('group_appointment.create');

    actingAs($user);

    $request = CreateBookingGroupRequestFactory::new()->create();

    livewire(CreateBookingGroup::class)
        ->fillForm($request)
        ->call('create')
        ->assertHasNoFormErrors();

    $bookingGroup = BookingGroup::first();

    expect($bookingGroup->created_by_id)->toBe($user->id);
    expect($bookingGroup->last_updated_by_id)->toBe($user->id);

    $undoRepeaterFake();
});

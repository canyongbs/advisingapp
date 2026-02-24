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

use AdvisingApp\MeetingCenter\Filament\Resources\BookingGroups\Pages\EditBookingGroup;
use AdvisingApp\MeetingCenter\Models\BookingGroup;
use AdvisingApp\MeetingCenter\Tests\Tenant\Filament\Resources\BookingGroups\Pages\RequestFactory\EditBookingGroupRequestFactory;
use AdvisingApp\Team\Models\Team;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('can render with proper permission', function () {
    $user = User::factory()->create();

    $bookingGroup = BookingGroup::factory()
        ->for(User::factory(), 'createdBy')
        ->create();

    actingAs($user);

    get(EditBookingGroup::getUrl([
        'record' => $bookingGroup->getRouteKey(),
    ]))
        ->assertForbidden();

    $user->givePermissionTo('group_appointment.view-any');
    $user->givePermissionTo('group_appointment.*.update');

    $user->refresh();

    get(EditBookingGroup::getUrl([
        'record' => $bookingGroup->getRouteKey(),
    ]))
        ->assertSuccessful();
});

it('validates the inputs', function (EditBookingGroupRequestFactory $data, array $errors) {
    asSuperAdmin();

    $user = User::factory()->create();

    $bookingGroup = BookingGroup::factory()->for($user, 'createdBy')->create();
    $request = EditBookingGroupRequestFactory::new($data)->create();

    livewire(EditBookingGroup::class, [
        'record' => $bookingGroup->getRouteKey(),
    ])
        ->fillForm($request)
        ->call('save')
        ->assertHasFormErrors($errors);

    assertDatabaseMissing(
        BookingGroup::class,
        $request
    );
})->with([
    'name required' => fn () => [
        EditBookingGroupRequestFactory::new()->state(['name' => null]),
        ['name' => 'required'],
    ],
    'name string' => fn () => [
        EditBookingGroupRequestFactory::new()->state(['name' => 1]),
        ['name' => 'string'],
    ],
    'name max' => fn () => [
        EditBookingGroupRequestFactory::new()->state(['name' => str()->random(256)]),
        ['name' => 'max'],
    ],
    'slug required' => fn () => [
        EditBookingGroupRequestFactory::new()->state(['slug' => null]),
        ['slug' => 'required'],
    ],
    'description string' => fn () => [
        EditBookingGroupRequestFactory::new()->state(['description' => 1]),
        ['description' => 'string'],
    ],
    'description max' => fn () => [
        EditBookingGroupRequestFactory::new()->state(['description' => str()->random(65536)]),
        ['description' => 'max'],
    ],
]);

it('can edit a booking group', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('group_appointment.view-any');
    $user->givePermissionTo('group_appointment.*.update');

    actingAs($user);

    $users = User::factory()->count(3)->create();
    $teams = Team::factory()->count(2)->create();

    $bookingGroup = BookingGroup::factory()->for($user, 'createdBy')->create();

    $bookingGroup->users()->attach($users);
    $bookingGroup->teams()->attach($teams);

    $request = EditBookingGroupRequestFactory::new()->state([
        'name' => 'Updated Name',
        'description' => 'Updated Description',
    ])->create();

    livewire(EditBookingGroup::class, [
        'record' => $bookingGroup->getRouteKey(),
    ])
        ->fillForm($request)
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertHasNoErrors();

    $bookingGroup->refresh();

    expect($bookingGroup->name)->toBe('Updated Name');
    expect($bookingGroup->description)->toBe('Updated Description');
});

it('tracks last_updated_by user correctly', function () {
    $creator = User::factory()->create();
    $editor = User::factory()->create();

    $editor->givePermissionTo('group_appointment.view-any');
    $editor->givePermissionTo('group_appointment.*.update');

    $bookingGroup = BookingGroup::factory()->for($creator, 'createdBy')->create();

    expect($bookingGroup->created_by_id)->toBe($creator->id);
    expect($bookingGroup->last_updated_by_id)->toBe(null);

    actingAs($editor);

    $request = EditBookingGroupRequestFactory::new()->create();

    livewire(EditBookingGroup::class, [
        'record' => $bookingGroup->getRouteKey(),
    ])
        ->fillForm($request)
        ->call('save')
        ->assertHasNoFormErrors();

    $bookingGroup->refresh();

    expect($bookingGroup->created_by_id)->toBe($creator->id);
    expect($bookingGroup->last_updated_by_id)->toBe($editor->id);
});

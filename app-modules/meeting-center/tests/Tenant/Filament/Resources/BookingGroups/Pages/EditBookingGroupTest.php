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

use AdvisingApp\MeetingCenter\Filament\Resources\BookingGroups\Pages\EditBookingGroup;
use AdvisingApp\MeetingCenter\Models\BookingGroup;
use AdvisingApp\MeetingCenter\Models\Calendar;
use AdvisingApp\MeetingCenter\Tests\Tenant\Filament\Resources\BookingGroups\Pages\RequestFactory\EditBookingGroupRequestFactory;
use AdvisingApp\Team\Models\Department;
use App\Features\BookingGroupNameCitextFeature;
use App\Filament\Forms\Components\UserSelect;
use App\Models\Authenticatable;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

it('can render with proper permission', function (): void {
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

it('validates the inputs', function (EditBookingGroupRequestFactory $data, array $errors): void {
    asSuperAdmin();

    $user = User::factory()->create();

    $bookingGroup = BookingGroup::factory()->for($user, 'createdBy')->create();
    $request = EditBookingGroupRequestFactory::new($data)->create();

    Livewire::test(EditBookingGroup::class, [
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
    'name required' => fn (): array => [
        EditBookingGroupRequestFactory::new()->state(['name' => null]),
        ['name' => 'required'],
    ],
    'name string' => fn (): array => [
        EditBookingGroupRequestFactory::new()->state(['name' => 1]),
        ['name' => 'string'],
    ],
    'name max' => fn (): array => [
        EditBookingGroupRequestFactory::new()->state(['name' => str()->random(256)]),
        ['name' => 'max'],
    ],
    'slug required' => fn (): array => [
        EditBookingGroupRequestFactory::new()->state(['slug' => null]),
        ['slug' => 'required'],
    ],
    'description string' => fn (): array => [
        EditBookingGroupRequestFactory::new()->state(['description' => 1]),
        ['description' => 'string'],
    ],
    'description max' => fn (): array => [
        EditBookingGroupRequestFactory::new()->state(['description' => str()->random(65536)]),
        ['description' => 'max'],
    ],
    'meeting owner required' => fn (): array => [
        EditBookingGroupRequestFactory::new(),
        ['meeting_owner_id' => 'required'],
    ],
]);

it('can edit a booking group', function (): void {
    $user = User::factory()->create();

    $user->givePermissionTo('group_appointment.view-any');
    $user->givePermissionTo('group_appointment.*.update');

    actingAs($user);

    $users = User::factory()->count(3)->create();
    $departments = Department::factory()->count(2)->create();
    $meetingOwner = $users->first();
    Calendar::factory()->for($meetingOwner)->create(['provider_id' => 'owner-calendar-id']);

    $bookingGroup = BookingGroup::factory()->for($user, 'createdBy')->create([
        'meeting_owner_id' => $meetingOwner->id,
    ]);

    $bookingGroup->users()->attach($users);
    $bookingGroup->departments()->attach($departments);

    $request = EditBookingGroupRequestFactory::new()->state([
        'name' => 'Updated Name',
        'description' => 'Updated Description',
        'users' => $users->pluck('id')->toArray(),
        'departments' => $departments->pluck('id')->toArray(),
        'meeting_owner_id' => $meetingOwner->id,
    ])->create();

    Livewire::test(EditBookingGroup::class, [
        'record' => $bookingGroup->getRouteKey(),
    ])
        ->fillForm($request)
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertHasNoErrors();

    $bookingGroup->refresh();

    expect($bookingGroup->name)->toBe('Updated Name');
    expect($bookingGroup->description)->toBe('Updated Description');
    expect($bookingGroup->meeting_owner_id)->toBe($meetingOwner->id);
});

it('tracks last_updated_by user correctly', function (): void {
    $creator = User::factory()->create();
    $editor = User::factory()->create();

    $editor->givePermissionTo('group_appointment.view-any');
    $editor->givePermissionTo('group_appointment.*.update');

    $meetingOwner = User::factory()->create();
    Calendar::factory()->for($meetingOwner)->create(['provider_id' => 'owner-calendar-id']);

    $bookingGroup = BookingGroup::factory()->for($creator, 'createdBy')->create([
        'meeting_owner_id' => $meetingOwner->id,
    ]);
    $bookingGroup->users()->attach($meetingOwner);

    expect($bookingGroup->created_by_id)->toBe($creator->id);
    expect($bookingGroup->last_updated_by_id)->toBe(null);

    actingAs($editor);

    $request = EditBookingGroupRequestFactory::new()->state([
        'users' => [$meetingOwner->id],
        'meeting_owner_id' => $meetingOwner->id,
    ])->create();

    Livewire::test(EditBookingGroup::class, [
        'record' => $bookingGroup->getRouteKey(),
    ])
        ->fillForm($request)
        ->call('save')
        ->assertHasNoFormErrors();

    $bookingGroup->refresh();

    expect($bookingGroup->created_by_id)->toBe($creator->id);
    expect($bookingGroup->last_updated_by_id)->toBe($editor->id);
});

it('blocks save when meeting owner is no longer in selected users or departments', function (): void {
    asSuperAdmin();

    $meetingOwner = User::factory()->create();
    Calendar::factory()->for($meetingOwner)->create(['provider_id' => 'owner-calendar-id']);

    $bookingGroup = BookingGroup::factory()
        ->for(User::factory(), 'createdBy')
        ->create(['meeting_owner_id' => $meetingOwner->id]);

    $bookingGroup->users()->attach($meetingOwner);

    $request = EditBookingGroupRequestFactory::new()->state([
        'users' => [],
        'departments' => [],
        'meeting_owner_id' => $meetingOwner->id,
    ])->create();

    Livewire::test(EditBookingGroup::class, [
        'record' => $bookingGroup->getRouteKey(),
    ])
        ->fillForm($request)
        ->call('save')
        ->assertHasFormErrors(['meeting_owner_id']);
});

it('validates meeting owner must have a connected calendar', function (): void {
    asSuperAdmin();

    $meetingOwner = User::factory()->create();
    $bookingGroup = BookingGroup::factory()
        ->for(User::factory(), 'createdBy')
        ->create([
            'meeting_owner_id' => $meetingOwner->id,
        ]);
    $bookingGroup->users()->attach($meetingOwner);

    $request = EditBookingGroupRequestFactory::new()->state([
        'users' => [$meetingOwner->id],
        'meeting_owner_id' => $meetingOwner->id,
    ])->create();

    Livewire::test(EditBookingGroup::class, [
        'record' => $bookingGroup->getRouteKey(),
    ])
        ->fillForm($request)
        ->call('save')
        ->assertHasFormErrors(['meeting_owner_id']);
});

it('users UserSelect does not show admin users in options by default on EditBookingGroup', function (): void {
    asSuperAdmin();

    $regularUser = User::factory()->create();
    $adminUser = User::factory()->create();
    $adminUser->assignRole(Authenticatable::SUPER_ADMIN_ROLE);

    $bookingGroup = BookingGroup::factory()
        ->for(User::factory(), 'createdBy')
        ->create();

    Livewire::test(EditBookingGroup::class, [
        'record' => $bookingGroup->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertFormFieldExists('users', checkFieldUsing: function (UserSelect $field) use ($regularUser, $adminUser): bool {
            return ! empty($field->getSearchResults($regularUser->name))
                && empty($field->getSearchResults($adminUser->name));
        });
});

it('users UserSelect shows all users when filter_admins_from_selection config is false on EditBookingGroup', function (): void {
    Config::set('app.filter_admins_from_selection', false);

    asSuperAdmin();

    $adminUser = User::factory()->create();
    $adminUser->assignRole(Authenticatable::SUPER_ADMIN_ROLE);

    $bookingGroup = BookingGroup::factory()
        ->for(User::factory(), 'createdBy')
        ->create();

    Livewire::test(EditBookingGroup::class, [
        'record' => $bookingGroup->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertFormFieldExists('users', checkFieldUsing: function (UserSelect $field) use ($adminUser): bool {
            return ! empty($field->getSearchResults($adminUser->name));
        });
});

test('does not allow updating a booking group to a name matching another booking group case-insensitively', function (): void {
    asSuperAdmin();

    BookingGroup::factory()->create(['name' => 'Advising Team']);
    $bookingGroup = BookingGroup::factory()->create(['name' => 'Enrollment Team']);

    Livewire::test(EditBookingGroup::class, ['record' => $bookingGroup->getRouteKey()])
        ->fillForm(['name' => 'advising team'])
        ->call('save')
        ->assertHasFormErrors(['name' => 'unique']);
});

test('allows updating a booking group to a name freed up by a soft-deleted booking group case-insensitively', function (): void {
    asSuperAdmin();

    $deletedBookingGroup = BookingGroup::factory()->create(['name' => 'Advising Team']);
    DB::table('booking_groups')->where('id', $deletedBookingGroup->getKey())->update(['deleted_at' => now()]);

    $bookingGroup = BookingGroup::factory()->create(['name' => 'Enrollment Team']);

    Livewire::test(EditBookingGroup::class, ['record' => $bookingGroup->getRouteKey()])
        ->fillForm(['name' => 'advising team'])
        ->call('save')
        ->assertHasNoFormErrors(['name']);
});

test('rejects updating a booking group to a soft-deleted name before the citext migration activates', function (): void {
    BookingGroupNameCitextFeature::deactivate();

    asSuperAdmin();

    $deletedBookingGroup = BookingGroup::factory()->create(['name' => 'Advising Team']);
    DB::table('booking_groups')->where('id', $deletedBookingGroup->getKey())->update(['deleted_at' => now()]);

    $bookingGroup = BookingGroup::factory()->create(['name' => 'Enrollment Team']);

    Livewire::test(EditBookingGroup::class, ['record' => $bookingGroup->getRouteKey()])
        ->fillForm(['name' => 'advising team'])
        ->call('save')
        ->assertHasFormErrors(['name' => 'unique']);

    expect($bookingGroup->refresh()->name)->toBe('Enrollment Team');
});

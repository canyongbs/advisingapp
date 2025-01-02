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

use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Models\User;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Filters\TrashedFilter;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function PHPUnit\Framework\assertNotInstanceOf;
use function Tests\asSuperAdmin;

it('show trashed filter only if user has user restore permission', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('user.view-any', 'user.*.view', 'user.*.restore');
    actingAs($user);

    livewire(ListUsers::class)
        ->assertTableFilterExists(TrashedFilter::class);
});

it('do not show trashed filter if user does not has restore permission', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('user.view-any', 'user.*.view');
    actingAs($user);

    assertNotInstanceOf(
        BaseFilter::class,
        livewire(ListUsers::class)->instance()->getTable()->getFilter(TrashedFilter::class),
        sprintf('Failed asserting that a table filter with name [%s] does not exist on the [%s] component.', TrashedFilter::class, ListUsers::class)
    );
});

it('do not show soft deleted users when filter is not selected', function () {
    asSuperAdmin();
    $users = User::factory()->count(3)->create();

    // Soft-delete one of the users
    $softDeletedUser = $users->first();
    $softDeletedUser->delete();
    $nonDeletedUserRecords = User::get();
    $softDeletedUserRecords = User::onlyTrashed()->get();

    livewire(ListUsers::class)
        ->removeTableFilters()
        ->assertCanSeeTableRecords($nonDeletedUserRecords)
        ->assertCanNotSeeTableRecords($softDeletedUserRecords);
});

it('can see soft deleted, non soft deleted records and status column only if the filter is on', function () {
    asSuperAdmin();
    $users = User::factory()->count(3)->create();

    // Soft-delete one of the users
    $softDeletedUser = $users->first();
    $softDeletedUser->delete();

    livewire(ListUsers::class)
        ->filterTable(TrashedFilter::class)
        ->assertTableColumnExists('deleted_at')
        ->assertCanSeeTableRecords($users);
});

it('Show restore action only if user has permission to restore user', function () {
    $user = User::factory()->create();

    $user->givePermissionTo('user.view-any', 'user.*.view', 'user.*.restore');
    actingAs($user);

    $users = User::factory()->count(3)->create();

    // Soft-delete one of the users
    $softDeletedUser = $users->first();
    $softDeletedUser->delete();
    $softDeletedUserRecords = User::onlyTrashed()->get();

    livewire(ListUsers::class)
        ->filterTable(TrashedFilter::class, 0)
        ->assertCanSeeTableRecords($softDeletedUserRecords)
        ->assertTableActionExists(RestoreAction::class);
});

it('check if restore feature works as expected', function () {
    asSuperAdmin();
    $user = User::factory()->create();

    // Soft-delete the user
    $user->delete();
    $softDeletedUserRecord = User::onlyTrashed()->get();
    $trashedUserRecord = User::onlyTrashed()->first();

    livewire(ListUsers::class)
        ->filterTable(TrashedFilter::class, 0)
        ->assertCanSeeTableRecords($softDeletedUserRecord)
        ->callTableAction(RestoreAction::class, $trashedUserRecord);

    expect($user->refresh())->deleted_at->toBe(null);
});

it('check if email EmailNotInUseOrSoftDeleted validations works properly while creating new user with email that is deleted', function () {
    asSuperAdmin();
    $user = User::factory()->create();

    // Soft-delete the user
    $user->delete();

    livewire(CreateUser::class)
        ->fillForm([
            'name' => 'Tester',
            'email' => $user->email,
        ])
        ->call('create')
        ->assertHasFormErrors(['email' => 'An archived user with this email address already exists. Please contact an administrator to restore this user or use a different email address.']);
});

it('check if email EmailNotInUseOrSoftDeleted validations works properly while creating user with email that is already in use', function () {
    asSuperAdmin();
    $first = User::factory()->create();

    livewire(CreateUser::class)
        ->fillForm([
            'name' => 'Tester',
            'email' => $first->email,
        ])
        ->call('create')
        ->assertHasFormErrors(['email' => "A user with this email address already exists. Please use a different email address or contact your administrator if you need to modify this user's account."]);
});

it('check if email EmailNotInUseOrSoftDeleted validations works properly while editing user with email that is deleted', function () {
    asSuperAdmin();
    $first = User::factory()->create();
    $second = User::factory()->create();

    // Soft-delete the user
    $first->delete();

    livewire(EditUser::class, [
        'record' => $second->getRouteKey(),
    ])
        ->fillForm([
            'email' => $first->email,
        ])
        ->call('save')
        ->assertHasFormErrors(['email' => 'An archived user with this email address already exists. Please contact an administrator to restore this user or use a different email address.']);
});

it('check if email EmailNotInUseOrSoftDeleted validations works properly while editing user with email that is already in use', function () {
    asSuperAdmin();
    $first = User::factory()->create();
    $second = User::factory()->create();

    livewire(EditUser::class, [
        'record' => $second->getRouteKey(),
    ])
        ->fillForm([
            'email' => $first->email,
        ])
        ->call('save')
        ->assertHasFormErrors(['email' => "A user with this email address already exists. Please use a different email address or contact your administrator if you need to modify this user's account."]);
});

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

use AdvisingApp\Team\Filament\Resources\Departments\DepartmentResource;
use AdvisingApp\Team\Filament\Resources\Departments\Pages\EditDepartment;
use AdvisingApp\Team\Filament\Resources\Departments\Pages\ViewDepartment;
use AdvisingApp\Team\Filament\Resources\Departments\RelationManagers\UsersRelationManager;
use AdvisingApp\Team\Models\Department;
use App\Features\RenameTeamToDepartmentFeature;
use App\Models\Authenticatable;
use App\Models\User;
use Filament\Actions\AssociateAction;
use Filament\Forms\Components\Select;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

// Permission Tests

test('EditDepartment is gated with proper access control', function () {
    $user = User::factory()->create();

    $department = Department::factory()->create();

    actingAs($user)
        ->get(
            DepartmentResource::getUrl('edit', [
                'record' => $department,
            ])
        )->assertForbidden();

    livewire(EditDepartment::class, [
        'record' => $department->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo(RenameTeamToDepartmentFeature::active() ? 'department.view-any' : 'team.view-any');
    $user->givePermissionTo(RenameTeamToDepartmentFeature::active() ? 'department.*.update' : 'team.*.update');

    actingAs($user)
        ->get(
            DepartmentResource::getUrl('edit', [
                'record' => $department,
            ])
        )->assertSuccessful();

    // TODO: Finish these tests to ensure changes are allowed
    /** @var Department $request */
    $request = Department::factory()->make();

    livewire(EditDepartment::class, [
        'record' => $department->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    $department->refresh();

    expect($department->name)->toEqual($request->name)
        ->and($department->description)->toEqual($request->description);
});

// Non Super Admin Users can be added to a department test

test('Non Super Admin Users can be added to a department', function () {
    $user = User::factory()->create();
    $department = Department::factory()->has(User::factory()->count(1))->create();

    $user->givePermissionTo(RenameTeamToDepartmentFeature::active() ? 'department.view-any' : 'team.view-any');
    $user->givePermissionTo(RenameTeamToDepartmentFeature::active() ? 'department.*.update' : 'team.*.update');
    $user->givePermissionTo('user.view-any');
    $user->givePermissionTo('user.*.update');

    actingAs($user)
        ->get(
            DepartmentResource::getUrl('edit', [
                'record' => $department,
            ])
        )->assertSuccessful();

    livewire(UsersRelationManager::class, [
        'ownerRecord' => $department,
        'pageClass' => EditDepartment::class,
    ])
        ->callTableAction(
            AssociateAction::class,
            data: ['recordId' => $user->getKey()]
        )->assertSuccessful();
});

// Super Admin Users cannot be added to a department

test('Super Admin Users cannot be added to a department', function () {
    $user = User::factory()->create();
    $superAdmin = User::factory()->create();
    $department = Department::factory()->create();

    $user->givePermissionTo(RenameTeamToDepartmentFeature::active() ? 'department.view-any' : 'team.view-any');
    $user->givePermissionTo(RenameTeamToDepartmentFeature::active() ? 'department.*.update' : 'team.*.update');
    $user->givePermissionTo('user.view-any');
    $user->givePermissionTo('user.*.update');

    $superAdmin->assignRole(Authenticatable::SUPER_ADMIN_ROLE);

    actingAs($user)
        ->get(
            DepartmentResource::getUrl('edit', [
                'record' => $department,
            ])
        )->assertSuccessful();

    livewire(UsersRelationManager::class, [
        'ownerRecord' => $department,
        'pageClass' => EditDepartment::class,
    ])
        ->callTableAction(
            AssociateAction::class,
            data: ['recordId' => $superAdmin->getKey()]
        )
        ->assertHasTableActionErrors(['recordId']);
});

//Super Admin Users do not show up in UsersRelationManager for Departments search results

test('Super Admin Users do not show up in UsersRelationManager for Departments search results', function () {
    $user = User::factory()->create();
    $superAdmin = User::factory()->create();
    $department = Department::factory()->create();

    $user->givePermissionTo(RenameTeamToDepartmentFeature::active() ? 'department.view-any' : 'team.view-any');
    $user->givePermissionTo(RenameTeamToDepartmentFeature::active() ? 'department.*.update' : 'team.*.update');
    $user->givePermissionTo('user.view-any');
    $user->givePermissionTo('user.*.update');

    $superAdmin->assignRole(Authenticatable::SUPER_ADMIN_ROLE);

    actingAs($user)
        ->get(
            DepartmentResource::getUrl('edit', [
                'record' => $department,
            ])
        )->assertSuccessful();

    livewire(UsersRelationManager::class, [
        'ownerRecord' => $department,
        'pageClass' => EditDepartment::class,
    ])
        ->mountTableAction(AssociateAction::class)
        ->assertFormFieldExists('recordId', checkFieldUsing: function (Select $select) use ($superAdmin) {
            $options = $select->getSearchResults($superAdmin->name);

            return empty($options) ? true : false;
        })->assertSuccessful();
});

// The associate/dissociate/attach/detach actions on relation managers are gated by the
// "update" ability of the owner record (see FilamentServiceProvider). On the read-only-able
// view page, a user who can only view the department must not see the associate action.
test('the associate action in the department users relation manager is gated by the department update permission', function () {
    $department = Department::factory()->create();

    $user = User::factory()->create();
    $user->givePermissionTo(RenameTeamToDepartmentFeature::active() ? 'department.view-any' : 'team.view-any');
    $user->givePermissionTo(RenameTeamToDepartmentFeature::active() ? 'department.*.view' : 'team.*.view');
    $user->givePermissionTo('user.view-any');

    actingAs($user);

    livewire(UsersRelationManager::class, [
        'ownerRecord' => $department,
        'pageClass' => ViewDepartment::class,
    ])->assertTableActionHidden(AssociateAction::class);

    $user->givePermissionTo(RenameTeamToDepartmentFeature::active() ? 'department.*.update' : 'team.*.update');

    livewire(UsersRelationManager::class, [
        'ownerRecord' => $department,
        'pageClass' => ViewDepartment::class,
    ])->assertTableActionVisible(AssociateAction::class);
});

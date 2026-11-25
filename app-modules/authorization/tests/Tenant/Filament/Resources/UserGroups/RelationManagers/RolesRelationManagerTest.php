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

use AdvisingApp\Authorization\Filament\Resources\UserGroups\Pages\EditUserGroup;
use AdvisingApp\Authorization\Filament\Resources\UserGroups\RelationManagers\RolesRelationManager;
use AdvisingApp\Authorization\Models\Role;
use AdvisingApp\Authorization\Models\UserGroup;
use App\Features\UserGroupsFeature;
use App\Models\Authenticatable;
use App\Models\User;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Select;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Tests\asSuperAdmin;

it('can attach roles to a user group', function (): void {
    asSuperAdmin();

    $userGroup = UserGroup::factory()->create();
    $roles = Role::factory()->count(2)->create();

    expect($userGroup->roles()->count())->toBe(0);

    Livewire::test(RolesRelationManager::class, ['ownerRecord' => $userGroup, 'pageClass' => EditUserGroup::class])
        ->callAction(TestAction::make(AttachAction::class)->table(), data: [
            'recordId' => $roles->modelKeys(),
        ])
        ->assertHasNoActionErrors();

    expect($userGroup->roles()->pluck('roles.id')->all())
        ->toEqualCanonicalizing($roles->modelKeys());
});

it('can detach a role from a user group', function (): void {
    asSuperAdmin();

    $userGroup = UserGroup::factory()->create();
    $role = Role::factory()->create();
    $userGroup->assignRole($role);

    Livewire::test(RolesRelationManager::class, ['ownerRecord' => $userGroup, 'pageClass' => EditUserGroup::class])
        ->callAction(TestAction::make(DetachAction::class)->table($role));

    expect($userGroup->roles()->count())->toBe(0);
});

it('bulk detaches roles from a user group', function (): void {
    asSuperAdmin();

    $userGroup = UserGroup::factory()->create();
    $roles = Role::factory()->count(2)->create();
    $userGroup->assignRole($roles);

    Livewire::test(RolesRelationManager::class, ['ownerRecord' => $userGroup, 'pageClass' => EditUserGroup::class])
        ->selectTableRecords($roles)
        ->callAction(TestAction::make(DetachBulkAction::class)->table()->bulk());

    expect($userGroup->roles()->count())->toBe(0);
});

it('only offers non-admin web roles that are not already attached', function (): void {
    asSuperAdmin();

    $userGroup = UserGroup::factory()->create();

    $assignableRole = Role::factory()->create(['name' => 'Assignable Advisor']);
    $attachedRole = Role::factory()->create(['name' => 'Attached Advisor']);
    $apiRole = Role::factory()->create(['name' => 'Api Advisor', 'guard_name' => 'api']);
    $adminRoleIds = Role::query()->whereIn('name', Authenticatable::ADMIN_ROLES)->pluck('id')->all();

    $userGroup->assignRole($attachedRole);

    $options = [];
    $searchResults = [];

    Livewire::test(RolesRelationManager::class, ['ownerRecord' => $userGroup, 'pageClass' => EditUserGroup::class])
        ->mountAction(TestAction::make(AttachAction::class)->table())
        ->assertFormFieldExists('recordId', function (Select $field) use (&$options, &$searchResults): bool {
            $options = $field->getOptions();
            $searchResults = $field->getSearchResults('a');

            return true;
        });

    expect($adminRoleIds)->not->toBeEmpty();

    foreach ([$options, $searchResults] as $roleIds) {
        expect($roleIds)
            ->toHaveKey($assignableRole->getKey())
            ->not->toHaveKey($attachedRole->getKey())
            ->not->toHaveKey($apiRole->getKey());

        foreach ($adminRoleIds as $adminRoleId) {
            expect($roleIds)->not->toHaveKey($adminRoleId);
        }
    }
});

it('rejects admin roles, even for a super admin', function (string $adminRole): void {
    asSuperAdmin();

    $userGroup = UserGroup::factory()->create();

    $role = Role::query()->where('name', $adminRole)->where('guard_name', 'web')->firstOrFail();

    Livewire::test(RolesRelationManager::class, ['ownerRecord' => $userGroup, 'pageClass' => EditUserGroup::class])
        ->callAction(TestAction::make(AttachAction::class)->table(), data: [
            'recordId' => [$role->getKey()],
        ])
        ->assertHasActionErrors(['recordId.0']);

    expect($userGroup->roles()->count())->toBe(0);
})->with(Authenticatable::ADMIN_ROLES);

it('rejects roles that are not on the web guard', function (): void {
    asSuperAdmin();

    $userGroup = UserGroup::factory()->create();

    $role = Role::factory()->create(['guard_name' => 'api']);

    Livewire::test(RolesRelationManager::class, ['ownerRecord' => $userGroup, 'pageClass' => EditUserGroup::class])
        ->callAction(TestAction::make(AttachAction::class)->table(), data: [
            'recordId' => [$role->getKey()],
        ])
        ->assertHasActionErrors(['recordId.0']);

    expect($userGroup->roles()->count())->toBe(0);
});

it('grants the roles of a user group to its members', function (): void {
    asSuperAdmin();

    $userGroup = UserGroup::factory()->create();
    $user = User::factory()->create();
    $userGroup->users()->attach($user);

    $role = Role::factory()->create();

    expect($user->fresh()->hasRole($role))->toBeFalse();

    Livewire::test(RolesRelationManager::class, ['ownerRecord' => $userGroup, 'pageClass' => EditUserGroup::class])
        ->callAction(TestAction::make(AttachAction::class)->table(), data: [
            'recordId' => [$role->getKey()],
        ])
        ->assertHasNoActionErrors();

    expect($user->fresh()->hasRole($role))->toBeTrue();
});

describe('authorization', function (): void {
    it('requires the `role.view-any` permission to view the relation manager', function (): void {
        $userGroup = UserGroup::factory()->create();

        actingAs(User::factory()->create()->givePermissionTo('user_group.view-any', 'user_group.*.view', 'user_group.*.update'));

        expect(RolesRelationManager::canViewForRecord($userGroup, EditUserGroup::class))->toBeFalse();

        actingAs(User::factory()->create()->givePermissionTo('user_group.view-any', 'user_group.*.view', 'user_group.*.update', 'role.view-any'));

        expect(RolesRelationManager::canViewForRecord($userGroup, EditUserGroup::class))->toBeTrue();
    });

    it('hides the relation manager when the `UserGroupsFeature` is inactive', function (): void {
        asSuperAdmin();

        $userGroup = UserGroup::factory()->create();

        expect(RolesRelationManager::canViewForRecord($userGroup, EditUserGroup::class))->toBeTrue();

        UserGroupsFeature::deactivate();

        expect(RolesRelationManager::canViewForRecord($userGroup, EditUserGroup::class))->toBeFalse();
    });
});

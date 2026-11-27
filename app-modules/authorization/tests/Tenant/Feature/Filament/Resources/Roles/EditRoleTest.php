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

use AdvisingApp\Authorization\Filament\Resources\Roles\Pages\EditRole;
use AdvisingApp\Authorization\Filament\Resources\Roles\RoleResource;
use AdvisingApp\Authorization\Models\Role;
use AdvisingApp\Authorization\Models\UserGroup;
use App\Enums\Feature;
use App\Features\UserGroupsFeature;
use App\Models\Authenticatable;
use App\Models\SystemUser;
use App\Models\User;
use CanyonGBS\Common\Filament\Forms\Components\PermissionsMatrix;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertModelExists;
use function Pest\Laravel\assertModelMissing;
use function Pest\Laravel\get;
use function Tests\asSuperAdmin;
use function Tests\setEnterpriseAiEnabled;

test('EditRole does not allow duplicate role names case insensitively within a guard', function (): void {
    asSuperAdmin();

    $role = Role::factory()->create(['name' => 'First Role', 'guard_name' => 'web']);
    Role::factory()->create(['name' => 'Second Role', 'guard_name' => 'web']);

    // Editing a role to its own name in a different case is allowed (record is ignored).
    Livewire::test(EditRole::class, ['record' => $role->getRouteKey()])
        ->fillForm(['name' => 'first role'])
        ->call('save')
        ->assertHasNoFormErrors();

    // Colliding with another role's name case-insensitively is rejected.
    Livewire::test(EditRole::class, ['record' => $role->getRouteKey()])
        ->fillForm(['name' => 'SECOND role'])
        ->call('save')
        ->assertHasFormErrors(['name' => 'unique']);
});

describe('membership', function (): void {
    it('can add and remove users and groups', function (): void {
        asSuperAdmin();

        $role = Role::factory()->create(['guard_name' => 'web']);

        $keptUser = User::factory()->create();
        $removedUser = User::factory()->create();
        $addedUser = User::factory()->create();

        $keptGroup = UserGroup::factory()->create();
        $removedGroup = UserGroup::factory()->create();
        $addedGroup = UserGroup::factory()->create();

        $role->users()->attach([$keptUser->getKey(), $removedUser->getKey()]);
        $role->userGroups()->attach([$keptGroup->getKey(), $removedGroup->getKey()]);

        Livewire::test(EditRole::class, ['record' => $role->getRouteKey()])
            ->assertSchemaStateSet([
                'users' => [$keptUser->getKey(), $removedUser->getKey()],
                'userGroups' => [$keptGroup->getKey(), $removedGroup->getKey()],
            ])
            ->fillForm([
                'users' => [$keptUser->getKey(), $addedUser->getKey()],
                'userGroups' => [$keptGroup->getKey(), $addedGroup->getKey()],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $role->refresh();

        expect($role->users->modelKeys())->toEqualCanonicalizing([$keptUser->getKey(), $addedUser->getKey()])
            ->and($role->userGroups->modelKeys())->toEqualCanonicalizing([$keptGroup->getKey(), $addedGroup->getKey()]);
    });

    it('keeps the system users of an `api` role', function (): void {
        asSuperAdmin();

        $role = Role::factory()->create(['guard_name' => 'api']);
        $systemUser = SystemUser::factory()->create();

        $role->users()->attach($systemUser->getKey());

        Livewire::test(EditRole::class, ['record' => $role->getRouteKey()])
            ->assertSchemaStateSet(['users' => [$systemUser->getKey()]])
            ->fillForm(['description' => 'Updated description'])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($role->refresh()->users->modelKeys())->toBe([$systemUser->getKey()]);
    });

    it('does not add users of another access control', function (): void {
        asSuperAdmin();

        $role = Role::factory()->create(['guard_name' => 'api']);
        $user = User::factory()->create();

        Livewire::test(EditRole::class, ['record' => $role->getRouteKey()])
            ->fillForm(['users' => [$user->getKey()]])
            ->call('save')
            ->assertHasFormErrors(['users.0' => 'in']);

        expect($role->refresh()->users)->toBeEmpty();
    });

    it('hides the groups for `api` roles', function (): void {
        asSuperAdmin();

        $role = Role::factory()->create(['guard_name' => 'api']);

        Livewire::test(EditRole::class, ['record' => $role->getRouteKey()])
            ->assertFormFieldHidden('userGroups');
    });

    it('hides the groups for admin roles', function (): void {
        asSuperAdmin();

        $role = Role::query()->firstOrCreate([
            'name' => Authenticatable::PARTNER_ADMIN_ROLE,
            'guard_name' => 'web',
        ]);

        Livewire::test(EditRole::class, ['record' => $role->getRouteKey()])
            ->assertFormFieldHidden('userGroups');
    });

    it('keeps an archived group listed while it holds the role', function (): void {
        asSuperAdmin();

        $role = Role::factory()->create(['guard_name' => 'web']);

        $assignedArchivedGroup = UserGroup::factory()->create();
        $unassignedArchivedGroup = UserGroup::factory()->create();

        $role->userGroups()->attach($assignedArchivedGroup);

        $assignedArchivedGroup->archive();
        $unassignedArchivedGroup->archive();

        Livewire::test(EditRole::class, ['record' => $role->getRouteKey()])
            ->assertSchemaStateSet(['userGroups' => [$assignedArchivedGroup->getKey()]])
            ->assertFormFieldExists('userGroups', function (Select $field) use ($assignedArchivedGroup, $unassignedArchivedGroup): bool {
                expect($field->getOptions())->toHaveKey($assignedArchivedGroup->getKey())
                    ->not->toHaveKey($unassignedArchivedGroup->getKey());

                return true;
            })
            ->fillForm(['userGroups' => []])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($role->refresh()->userGroups)->toBeEmpty();
    });
    it('hides the users without the `user.view-any` permission and keeps them on save', function (): void {
        $role = Role::factory()->create(['guard_name' => 'web']);
        $member = User::factory()->create();

        $role->users()->attach($member);

        actingAs(user(permissions: ['role.view-any', 'role.*.view', 'role.*.update']));

        Livewire::test(EditRole::class, ['record' => $role->getRouteKey()])
            ->assertFormFieldHidden('users')
            ->assertSchemaStateSet(['users' => []])
            ->fillForm(['description' => 'Updated description'])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($role->refresh()->users->modelKeys())->toBe([$member->getKey()]);
    });

    it('does not offer or remove the admins a user can not see', function (): void {
        $role = Role::factory()->create(['guard_name' => 'web']);
        $member = User::factory()->create();
        $superAdmin = User::factory()->create();

        $superAdmin->assignRole(Role::superAdmin()->get());
        $role->users()->attach([$member->getKey(), $superAdmin->getKey()]);

        actingAs(user(permissions: ['role.view-any', 'role.*.view', 'role.*.update', 'user.view-any']));

        Livewire::test(EditRole::class, ['record' => $role->getRouteKey()])
            ->assertSchemaStateSet(['users' => [$member->getKey()]])
            ->assertFormFieldExists('users', function (Select $field) use ($member, $superAdmin): bool {
                expect($field->getOptions())->toHaveKey($member->getKey())
                    ->not->toHaveKey($superAdmin->getKey())
                    ->and($field->getSearchResults($superAdmin->name))->not->toHaveKey($superAdmin->getKey());

                return true;
            })
            ->fillForm(['users' => []])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($role->refresh()->users->modelKeys())->toBe([$superAdmin->getKey()]);
    });

    it('hides the groups without the `user_group.view-any` permission and keeps them on save', function (): void {
        $role = Role::factory()->create(['guard_name' => 'web']);
        $group = UserGroup::factory()->create();

        $role->userGroups()->attach($group);

        actingAs(user(permissions: ['role.view-any', 'role.*.view', 'role.*.update']));

        Livewire::test(EditRole::class, ['record' => $role->getRouteKey()])
            ->assertFormFieldHidden('userGroups')
            ->assertSchemaStateSet(['userGroups' => []])
            ->fillForm(['description' => 'Updated description'])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($role->refresh()->userGroups->modelKeys())->toBe([$group->getKey()]);
    });

    it('keeps the groups on save when the `UserGroupsFeature` is inactive', function (): void {
        asSuperAdmin();

        $role = Role::factory()->create(['guard_name' => 'web']);
        $group = UserGroup::factory()->create();

        $role->userGroups()->attach($group);

        UserGroupsFeature::deactivate();

        Livewire::test(EditRole::class, ['record' => $role->getRouteKey()])
            ->assertFormFieldHidden('userGroups')
            ->assertSchemaStateSet(['userGroups' => []])
            ->fillForm(['description' => 'Updated description'])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($role->refresh()->userGroups->modelKeys())->toBe([$group->getKey()]);
    });
});

describe('admin roles', function (): void {
    it('does not allow renaming a role to an admin role name', function (): void {
        asSuperAdmin();

        $role = Role::factory()->create(['guard_name' => 'api']);

        Livewire::test(EditRole::class, ['record' => $role->getRouteKey()])
            ->fillForm(['name' => 'saas global admin'])
            ->call('save')
            ->assertHasFormErrors(['name']);

        expect($role->refresh()->name)->not->toBe('saas global admin');
    });

    it('allows saving an admin role under its own name', function (): void {
        asSuperAdmin();

        $role = Role::query()->firstOrCreate([
            'name' => Authenticatable::PARTNER_ADMIN_ROLE,
            'guard_name' => 'web',
        ]);

        Livewire::test(EditRole::class, ['record' => $role->getRouteKey()])
            ->fillForm(['description' => 'Updated description'])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($role->refresh()->description)->toBe('Updated description');
    });
});

describe('deletion', function (): void {
    it('hides the `DeleteAction` when a role is assigned through a populated user group', function (): void {
        asSuperAdmin();

        $role = Role::factory()->create(['guard_name' => 'web']);
        $userGroup = UserGroup::factory()->create();
        $user = User::factory()->create();

        $userGroup->assignRole($role);
        $userGroup->users()->attach($user);

        Livewire::test(EditRole::class, ['record' => $role->getRouteKey()])
            ->assertActionHidden(DeleteAction::class);

        assertModelExists($role);
    });

    it('denies deleting a role assigned through a populated user group', function (): void {
        asSuperAdmin();

        $role = Role::factory()->create(['guard_name' => 'web']);
        $userGroup = UserGroup::factory()->create();
        $user = User::factory()->create();

        $userGroup->assignRole($role);
        $userGroup->users()->attach($user);

        $response = Gate::forUser(auth()->user())->inspect('delete', $role);

        expect($response->denied())->toBeTrue()
            ->and($response->message())->toBe('You cannot delete a role that is assigned to a group with members.');
    });

    it('can delete a role assigned only through empty user groups', function (): void {
        asSuperAdmin();

        $role = Role::factory()->create(['guard_name' => 'web']);
        $userGroup = UserGroup::factory()->create();

        $userGroup->assignRole($role);

        Livewire::test(EditRole::class, ['record' => $role->getRouteKey()])
            ->callAction(DeleteAction::class);

        assertModelMissing($role);
    });

    it('can delete a role assigned only through deleted user groups', function (): void {
        asSuperAdmin();

        $role = Role::factory()->create(['guard_name' => 'web']);
        $userGroup = UserGroup::factory()->create();
        $user = User::factory()->create();

        $userGroup->assignRole($role);
        $userGroup->users()->attach($user);
        $userGroup->delete();

        Livewire::test(EditRole::class, ['record' => $role->getRouteKey()])
            ->callAction(DeleteAction::class);

        assertModelMissing($role);
    });

    it('can delete a role assigned through a populated user group when the `UserGroupsFeature` is inactive', function (): void {
        UserGroupsFeature::deactivate();

        asSuperAdmin();

        $role = Role::factory()->create(['guard_name' => 'web']);
        $userGroup = UserGroup::factory()->create();
        $user = User::factory()->create();

        $userGroup->assignRole($role);
        $userGroup->users()->attach($user);

        Livewire::test(EditRole::class, ['record' => $role->getRouteKey()])
            ->callAction(DeleteAction::class);

        assertModelMissing($role);
    });
});

describe('enterprise ai', function (): void {
    it('hides the Enterprise AI permission groups while Enterprise AI is disabled', function (): void {
        asSuperAdmin();

        $role = Role::factory()->create(['guard_name' => 'web']);

        $getAvailablePermissionGroupNames = function () use ($role): array {
            $availablePermissionGroupNames = [];

            Livewire::test(EditRole::class, ['record' => $role->getRouteKey()])
                ->assertFormFieldExists('permissions', function (PermissionsMatrix $field) use (&$availablePermissionGroupNames): bool {
                    $availablePermissionGroupNames = array_keys($field->getAvailablePermissions());

                    return true;
                });

            return $availablePermissionGroupNames;
        };

        expect(array_values(array_intersect($getAvailablePermissionGroupNames(), Feature::EnterpriseAi->getPermissionGroupNames())))
            ->toEqualCanonicalizing(Feature::EnterpriseAi->getPermissionGroupNames());

        setEnterpriseAiEnabled(false);

        $availablePermissionGroupNames = $getAvailablePermissionGroupNames();

        expect(array_intersect($availablePermissionGroupNames, Feature::EnterpriseAi->getPermissionGroupNames()))->toBeEmpty()
            ->and($availablePermissionGroupNames)->toContain('User', 'Role');
    });

    it('keeps a role\'s hidden Enterprise AI permissions when it is saved while Enterprise AI is disabled', function (): void {
        asSuperAdmin();

        $role = Role::factory()->create(['name' => 'Advisors', 'guard_name' => 'web']);
        $role->givePermissionTo(['prompt.view-any', 'user.view-any']);

        setEnterpriseAiEnabled(false);

        Livewire::test(EditRole::class, ['record' => $role->getRouteKey()])
            ->fillForm(['name' => 'Renamed Advisors'])
            ->call('save')
            ->assertHasNoFormErrors();

        $role->refresh();

        expect($role->name)->toBe('Renamed Advisors')
            ->and($role->hasPermissionTo('prompt.view-any'))->toBeTrue()
            ->and($role->hasPermissionTo('user.view-any'))->toBeTrue();
    });
});

describe('authorization', function (): void {
    it('does not let a non-super-admin edit an admin role', function (): void {
        $role = Role::query()->firstOrCreate([
            'name' => Authenticatable::PARTNER_ADMIN_ROLE,
            'guard_name' => 'web',
        ]);

        actingAs(user(permissions: ['role.view-any', 'role.*.view', 'role.*.update']));

        get(RoleResource::getUrl('edit', ['record' => $role]))
            ->assertNotFound();
    });

    it('lets a non-super-admin edit a role with the `role.*.update` permission', function (): void {
        $role = Role::factory()->create(['guard_name' => 'web']);

        actingAs(user(permissions: ['role.view-any', 'role.*.view', 'role.*.update']));

        get(RoleResource::getUrl('edit', ['record' => $role]))
            ->assertSuccessful();
    });
});

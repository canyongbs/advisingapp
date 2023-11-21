<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use App\Models\User;
use Assist\Authorization\Models\Role;
use Assist\Authorization\Models\RoleGroup;
use Assist\Authorization\Enums\ModelHasRolesViaEnum;

it('will remove the Role from all Users that were assigned via the RoleGroup', function () {
    // Given that we have some Users
    $users = User::factory()->count(5)->create();

    // A RoleGroup
    $roleGroup = RoleGroup::factory()->create();

    // A few Roles within that RoleGroup
    $rolesInGroup = Role::factory()->count(3)->create();
    $roleGroup->roles()->syncWithoutDetaching($rolesInGroup->pluck('id'));

    // And the Users are in the RoleGroup
    $roleGroup->users()->syncWithoutDetaching($users->pluck('id'));

    $roleGroup->users()->each(function (User $user) {
        expect($user->roles->count())->toBe(3);
    });

    // When a single Role is removed from the RoleGroup
    $roleToRemove = $rolesInGroup->first();
    $roleGroup->roles()->detach($roleToRemove->id);

    // Then all the Users should have that Role removed
    $roleGroup->users()->each(function (User $user) use ($roleToRemove) {
        expect($user->roles->count())->toBe(2);
        expect($user->roles->pluck('id'))->not->toContain($roleToRemove->id);
    });
});

it('will not remove any Role that was assigned directly to the User', function () {
    // Given that we have a User
    $user = User::factory()->create();

    // Two Role
    $assignedRole = Role::factory()->create();

    // A RoleGroup
    $roleGroup = RoleGroup::factory()->create();

    // And that User has been directly assigned a Role
    $user->assignRole($user->roles, $assignedRole);

    expect($user->roles->first()->pivot->via)->toBe(ModelHasRolesViaEnum::Direct->value);

    // If that Role also belongs to the RoleGroup
    $roleGroup->roles()->syncWithoutDetaching([$assignedRole->id]);

    // And the User is in the RoleGroup
    $user->roleGroups()->syncWithoutDetaching([$roleGroup->id]);

    // If the Role is removed from the RoleGroup
    $roleGroup->roles()->detach($assignedRole->id);

    // Then the User should still have that Role
    $user->refresh();

    expect($user->roles->count())->toBe(1);
    expect($user->roles->first()->id)->toBe($assignedRole->id);
});

it('will not remove the Role if the User is granted that Role via a different Role Group', function () {
    // Given that we have a User
    $user = User::factory()->create();

    // Two RoleGroups
    $roleGroups = RoleGroup::factory()->count(2)->create();

    // A Role that belongs to each RoleGroup
    $role = Role::factory()->create();

    $role->roleGroups()->syncWithoutDetaching($roleGroups->pluck('id'));

    // And the User belongs to each RoleGroup
    $user->roleGroups()->syncWithoutDetaching($roleGroups->pluck('id'));

    expect($user->roles->count())->toBe(1);
    expect($user->roleGroups->count())->toBe(2);

    // When the Role is removed from one of the RoleGroup
    $firstRoleGroup = $roleGroups->first();

    $firstRoleGroup->roles()->detach($role->id);

    // The User should still have this Role via the second RoleGroup
    expect($user->roles->count())->toBe(1);

    // But if the Role is removed from the second RoleGroup
    $secondRoleGroup = $roleGroups->last();

    $secondRoleGroup->roles()->detach($role->id);

    $user->refresh();

    // Then the User should no longer have this Role
    expect($user->roles->count())->toBe(0);
});

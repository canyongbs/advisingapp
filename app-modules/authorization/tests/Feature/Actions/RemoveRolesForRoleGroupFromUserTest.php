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

it('will remove all Roles in the RoleGroup when a User is removed from a RoleGroup', function () {
    // Given that we have a User
    $user = User::factory()->create();

    // A RoleGroup
    $roleGroup = RoleGroup::factory()->create();

    // A few Roles within that RoleGroup
    $rolesInGroup = Role::factory()->count(3)->create();
    $roleGroup->roles()->syncWithoutDetaching($rolesInGroup->pluck('id'));

    // And the User is in the RoleGroup
    $user->roleGroups()->syncWithoutDetaching($roleGroup->id);

    expect($user->roles->count())->toBe(3);

    // When the User is removed from the RoleGroup
    $user->roleGroups()->detach($roleGroup->id);

    // The User should no longer have the Roles in the RoleGroup
    $user->refresh();

    expect($user->roles->count())->toBe(0);
});

it('will not remove any Role that was assigned directly to the User', function () {
    // Given that we have a User
    $user = User::factory()->create();

    // Two Role
    $assignedRole = Role::factory()->create();
    $unassignedRole = Role::factory()->create();

    // A RoleGroup
    $roleGroup = RoleGroup::factory()->create();

    // And that User has been directly assigned one of the Role
    $user->assignRole($user->roles, $assignedRole);

    expect($user->roles->count())->toBe(1);

    // If the assigned and unassigned Role both belong to the RoleGroup
    $roleGroup->roles()->syncWithoutDetaching([$assignedRole->id, $unassignedRole->id]);

    // And the User is in the RoleGroup
    $user->roleGroups()->syncWithoutDetaching([$roleGroup->id]);

    // If the assigned Role is removed from the RoleGroup
    $roleGroup->roles()->detach($assignedRole->id);

    // Then the User should still have that Role
    $user->refresh();

    expect($user->roles->count())->toBe(2);
    expect($user->roles->first()->id)->toBe($assignedRole->id);

    // And if the unassigned Role is removed from the RoleGroup

    $roleGroup->roles()->detach($unassignedRole->id);

    // The User will lose the unassigned Role, but still have the assigned Role
    $user->refresh();

    expect($user->roles->count())->toBe(1);
    expect($user->roles->first()->id)->toBe($assignedRole->id);
});

it('will not remove a Role if the User is granted that Role via a different Role Group', function () {
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

    // When the User is removed from one of the RoleGroup
    $firstRoleGroup = $roleGroups->first();

    $firstRoleGroup->users()->detach($user->id);

    // The User should still have this Role via the second RoleGroup
    expect($user->roles->count())->toBe(1);

    // But if the Role is removed from the second RoleGroup
    $secondRoleGroup = $roleGroups->last();

    $secondRoleGroup->roles()->detach($role->id);

    $user->refresh();

    // Then the User should no longer have this Role
    expect($user->roles->count())->toBe(0);
});

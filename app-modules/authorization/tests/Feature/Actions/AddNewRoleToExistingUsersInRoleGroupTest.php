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

it('will add a newly attached role in the role group to any existing users in the role group', function () {
    // Given that we have a user
    $user = User::factory()->create();

    // A RoleGroup
    $roleGroup = RoleGroup::factory()->create();

    // A Role within that RoleGroup
    $roleInGroup = Role::factory()->create();
    $roleGroup->roles()->syncWithoutDetaching($roleInGroup->id);

    // And the User exists in the RoleGroup
    $user->roleGroups()->syncWithoutDetaching([$roleGroup->id]);

    expect($user->roles->count())->toBe(1);

    // When we add a new Role to the RoleGroup
    $newRoleInGroup = Role::factory()->create();
    $roleGroup->roles()->syncWithoutDetaching($newRoleInGroup->id);

    $user->refresh();

    // That Role should be given to the User via the RoleGroup
    expect($user->roles->count())->toBe(2);
});

it('will not overwrite an existing role that was directly assigned to the user', function () {
    // Given that we have a User
    $user = User::factory()->create();

    // Two Role
    $assignedRole = Role::factory()->create();
    $unassignedRole = Role::factory()->create();

    // A RoleGroup
    $roleGroup = RoleGroup::factory()->create();

    // And that User has been directly assigned a Role
    $user->assignRole($user->roles, $assignedRole);

    expect($user->roles->first()->pivot->via)->toBe(ModelHasRolesViaEnum::Direct->value);

    $roleGroup->roles()->syncWithoutDetaching([$unassignedRole->id]);

    // And the User is added to the RoleGroup
    $user->roleGroups()->syncWithoutDetaching([$roleGroup->id]);

    $user->refresh();

    expect($user->roles->count())->toBe(2);

    // When the Role that the User already has is added to the RoleGroup
    $roleGroup->roles()->syncWithoutDetaching([$assignedRole->id]);

    // Then the User should still have the assigned Role applied "directly"
    expect($user->roles()->where('id', $assignedRole->id)->first()->pivot->via)->toBe(ModelHasRolesViaEnum::Direct->value);

    // And the new Role inherited from the RoleGroup should be applied via the "role_group"
    expect($user->roles()->where('id', $unassignedRole->id)->first()->pivot->via)->toBe(ModelHasRolesViaEnum::RoleGroup->value);
});

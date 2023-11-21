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
use Assist\Authorization\Events\RoleRemovedFromUser;

it('will remove a user from any role group the role belonged to', function () {
    // Given that we have a user
    $user = User::factory()->create();

    // A RoleGroup
    $roleGroup = RoleGroup::factory()->create();

    // A Role within that RoleGroup
    $roleInGroup = Role::factory()->create();
    $roleGroup->roles()->syncWithoutDetaching($roleInGroup->id);

    // And we add the User to the RoleGroup
    $user->roleGroups()->syncWithoutDetaching([$roleGroup->id]);

    expect($user->roles->count())->toBe(1);
    expect($user->roleGroups->count())->toBe(1);

    // When we detach the Role from the User
    $user->roles()->detach($roleInGroup->id);
    RoleRemovedFromUser::dispatch($roleInGroup, $user);

    $user->refresh();

    // The User should no longer be in the RoleGroup that the Role belonged to
    expect($user->roles->count())->toBe(0);
    expect($user->roleGroups->count())->toBe(0);
});

it('will not remove a role group that the role is not associated with', function () {
    // Given that we have a user
    $user = User::factory()->create();

    // Two RoleGroup
    $roleGroup = RoleGroup::factory()->create();
    $otherRoleGroup = RoleGroup::factory()->create();

    // A Role within one of those RoleGroup
    $roleInGroup = Role::factory()->create();
    $roleGroup->roles()->syncWithoutDetaching($roleInGroup->id);

    // And we add the User to both RoleGroup
    $user->roleGroups()->syncWithoutDetaching([$roleGroup->id, $otherRoleGroup->id]);

    expect($user->roles->count())->toBe(1);
    expect($user->roleGroups->count())->toBe(2);

    // When we detach the Role from the User
    $user->roles()->detach($roleInGroup->id);
    RoleRemovedFromUser::dispatch($roleInGroup, $user);

    $user->refresh();

    // The User should no longer be in the RoleGroup that the Role belonged to
    expect($user->roles->count())->toBe(0);
    // But the User should still belong to the RoleGroup the Role did not belong to
    expect($user->roleGroups->count())->toBe(1);
    expect($user->roleGroups->first()->id)->toBe($otherRoleGroup->id);
});

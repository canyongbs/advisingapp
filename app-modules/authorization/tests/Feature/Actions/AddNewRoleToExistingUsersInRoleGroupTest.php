<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use App\Models\User;
use AdvisingApp\Authorization\Models\Role;
use AdvisingApp\Authorization\Models\RoleGroup;
use AdvisingApp\Authorization\Enums\ModelHasRolesViaEnum;

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

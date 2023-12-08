<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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
use AdvisingApp\Authorization\Events\RoleRemovedFromUser;

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

<?php

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

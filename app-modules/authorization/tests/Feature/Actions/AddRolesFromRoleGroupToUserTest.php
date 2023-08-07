<?php

use App\Models\User;
use Assist\Authorization\Models\Role;
use Assist\Authorization\Models\RoleGroup;
use Assist\Authorization\Enums\ModelHasRolesViaEnum;

it('will add any roles belonging to the RoleGroup to a newly attached User', function () {
    // Given that we have a user
    $user = User::factory()->create();

    // A RoleGroup
    $roleGroup = RoleGroup::factory()->create();

    // A few Roles within that RoleGroup
    $rolesInGroup = Role::factory()->count(3)->create();
    $roleGroup->roles()->syncWithoutDetaching($rolesInGroup->pluck('id'));

    expect($user->roles->count())->toBe(0);
    expect($user->roleGroups->count())->toBe(0);

    // When the User is added to the Role Group
    $user->roleGroups()->syncWithoutDetaching([$roleGroup->id]);

    $user->refresh();

    expect($user->roles->count())->toBe(3);
    expect($user->roleGroups->count())->toBe(1);
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

    // If that Role also happens to belong to the RoleGroup
    $roleGroup->roles()->syncWithoutDetaching([$assignedRole->id, $unassignedRole->id]);

    // When we attach the RoleGroup to the User
    $user->roleGroups()->syncWithoutDetaching([$roleGroup->id]);

    $user->refresh();

    // Then the User should still have the assigned Role applied "directly"
    expect($user->roles()->where('id', $assignedRole->id)->first()->pivot->via)->toBe(ModelHasRolesViaEnum::Direct->value);

    // And the new Role inherited from the RoleGroup should be applied via the "role_group"
    expect($user->roles()->where('id', $unassignedRole->id)->first()->pivot->via)->toBe(ModelHasRolesViaEnum::RoleGroup->value);
});

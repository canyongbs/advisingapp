<?php

namespace Assist\Authorization\Actions;

use App\Models\User;
use Assist\Authorization\Models\Role;
use Assist\Authorization\Models\RoleGroup;
use Assist\Authorization\Models\Pivots\RoleGroupRolePivot;

// TODO This should be queued
class RemoveRoleFromUsersInRoleGroup
{
    public function handle(RoleGroupRolePivot $pivot): void
    {
        $roleGroup = RoleGroup::findOrFail($pivot->role_group_id);
        $role = Role::findOrFail($pivot->role_id);

        $roleGroup->users()->each(function (User $user) use ($role, $roleGroup) {
            if ($this->roleShouldBeRemovedFromUser($user, $role, $roleGroup)) {
                $user->removeRole($role);
            }
        });
    }

    // TODO Extract this somewhere common as it's shared between this action and "RemoveRolesForRoleGroupFromUser"
    protected function roleShouldBeRemovedFromUser(User $user, Role $role, RoleGroup $roleGroup): bool
    {
        // If the user doesn't even have this role, no sense in removing
        if (! $user->hasRole($role)) {
            return false;
        }

        // If the user has this role directly, we should not remove it
        if ($user->hasBeenAssignedRoleDirectly($role)) {
            return false;
        }

        // If the role belongs to another role group that the user belongs to, we should not remove it
        if ($user->inheritsRoleFromAnotherRoleGroup($role, $roleGroup)) {
            return false;
        }

        return true;
    }
}

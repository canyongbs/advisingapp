<?php

namespace Assist\Authorization\Actions;

use App\Models\User;
use Assist\Authorization\Models\Role;
use Assist\Authorization\Models\RoleGroup;
use Assist\Authorization\Models\Pivots\RoleGroupPivot;

// TODO This should be queued
class RemoveRolesForRoleGroupFromUser
{
    public function handle(RoleGroupPivot $roleGroupPivot): void
    {
        $roleGroup = RoleGroup::findOrFail($roleGroupPivot->role_group_id);
        $user = User::findOrFail($roleGroupPivot->role_groupable_id);

        $roleGroup->roles()->each(function (Role $role) use ($user, $roleGroup) {
            if ($this->roleShouldBeRemovedFromUser($user, $role, $roleGroup)) {
                $user->removeRole($role);
            }
        });
    }

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

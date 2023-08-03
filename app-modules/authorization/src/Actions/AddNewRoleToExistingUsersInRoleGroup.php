<?php

namespace Assist\Authorization\Actions;

use App\Models\User;
use Assist\Authorization\Models\Role;
use Assist\Authorization\Models\RoleGroup;
use Assist\Authorization\Models\Pivots\RoleGroupRolePivot;

// TODO This should be queued
class AddNewRoleToExistingUsersInRoleGroup
{
    public function handle(RoleGroupRolePivot $pivot): void
    {
        $roleGroup = RoleGroup::findOrFail($pivot->role_group_id);
        $role = Role::findOrFail($pivot->role_id);

        $roleGroup->users()->each(function (User $user) use ($role) {
            if (! $user->hasRole($role)) {
                $user->assignRoleViaRoleGroup($role);
            }
        });
    }
}

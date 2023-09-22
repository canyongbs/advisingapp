<?php

namespace Assist\Authorization\Actions;

use App\Models\User;
use Assist\Authorization\Models\Role;
use Assist\Authorization\Models\RoleGroup;
use Assist\Authorization\Models\Pivots\RoleGroupUserPivot;

// TODO This should be queued
class AddRolesFromRoleGroupToUser
{
    public function handle(RoleGroupUserPivot $pivot): void
    {
        $roleGroup = RoleGroup::findOrFail($pivot->role_group_id);
        $user = User::findOrFail($pivot->user_id);

        $roleGroup->roles()->each(function (Role $role) use ($user) {
            if (! $user->hasRole($role)) {
                $user->assignRoleViaRoleGroup($role);
            }
        });
    }
}

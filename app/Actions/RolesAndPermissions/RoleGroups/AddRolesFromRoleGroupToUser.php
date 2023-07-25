<?php

namespace App\Actions\RolesAndPermissions\RoleGroups;

use App\Models\Role;
use App\Models\User;
use App\Models\RoleGroup;
use App\Enums\ModelHasRolesViaEnum;
use App\Models\Pivots\RoleGroupPivot;

// TODO This should be queued
class AddRolesFromRoleGroupToUser
{
    public function handle(RoleGroupPivot $roleGroupPivot): void
    {
        $roleGroup = RoleGroup::findOrFail($roleGroupPivot->role_group_id);
        $user = User::findOrFail($roleGroupPivot->role_groupable_id);

        $roleGroup->roles()->each(function (Role $role) use ($user) {
            if (! $user->hasRole($role)) {
                // TODO We should do this logic in one fell swoop
                // We'll just probably need to extend the `assignRole` method to make it
                // `assignRoleViaRoleGroup` and tuck the logic that follows into that method
                $user->assignRole([$user->roles, $role]);

                $user->roles()->where('id', $role->id)->first()->pivot->update([
                    'via' => ModelHasRolesViaEnum::RoleGroup,
                ]);
            }
        });
    }
}

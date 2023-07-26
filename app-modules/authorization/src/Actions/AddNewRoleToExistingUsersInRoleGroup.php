<?php

namespace Assist\Authorization\Actions;

use App\Models\User;
use Assist\Authorization\Models\Role;
use Assist\Authorization\Models\RoleGroup;
use Assist\Authorization\Enums\ModelHasRolesViaEnum;
use Assist\Authorization\Models\Pivots\RoleGroupPivot;

// TODO This should be queued
class AddNewRoleToExistingUsersInRoleGroup
{
    public function handle(RoleGroupPivot $roleGroupPivot): void
    {
        $roleGroup = RoleGroup::findOrFail($roleGroupPivot->role_group_id);
        $role = Role::findOrFail($roleGroupPivot->role_groupable_id);

        $roleGroup->users()->each(function (User $user) use ($role) {
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

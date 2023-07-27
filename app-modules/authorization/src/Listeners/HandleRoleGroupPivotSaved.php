<?php

namespace Assist\Authorization\Listeners;

use App\Models\User;
use Assist\Authorization\Models\Role;
use Assist\Authorization\Actions\AddRolesFromRoleGroupToUser;
use Assist\Authorization\Actions\AddNewRoleToExistingUsersInRoleGroup;

class HandleRoleGroupPivotSaved
{
    public function handle(object $event): void
    {
        // If a user was added to a role group, add the roles of the role group to the user
        // We won't re-assign any roles that already exist, and all records added via this method will indicate they were added "via" a role_group
        if ($event->roleGroupPivot->role_groupable_type === resolve(User::class)->getMorphClass()) {
            resolve(AddRolesFromRoleGroupToUser::class)->handle($event->roleGroupPivot);
        }

        // If a role was added to a role group, add this role to all users that belong to the role group
        // This role will then also be assigned "via" the role_group
        if ($event->roleGroupPivot->role_groupable_type === resolve(Role::class)->getMorphClass()) {
            resolve(AddNewRoleToExistingUsersInRoleGroup::class)->handle($event->roleGroupPivot);
        }
    }
}

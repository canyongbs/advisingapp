<?php

namespace Assist\Authorization\Listeners;

use App\Models\Role;
use App\Models\User;
use App\Actions\RolesAndPermissions\RoleGroups\RemoveRoleFromUsersInRoleGroup;
use App\Actions\RolesAndPermissions\RoleGroups\RemoveRolesForRoleGroupFromUser;

class HandleRoleGroupPivotDeleted
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        // TODO For some reason we do not have access to the `role_groupable_type` on the pivot record
        // (https://github.com/laravel/framework/issues/31658)
        // For the deleted event. We do have the `role_group_id` and the `role_groupable_id`.
        // On saved, this issue does not exist so we are able to determine model impacted.

        // I'm going to dig a bit deeper into the "why" of this and see if we can resolve it
        // As it currently exists, but if it seems to be too much of a pain we can simply
        // Make independent pivot tables and not have a polymorphic intermediate table.
        // role_role_group // roles groups -> roles
        // role_group_user // role groups -> users

        // if ($event->roleGroupPivot->role_groupable_type === resolve(User::class)->getMorphClass()) {
        //     resolve(RemoveRolesForRoleGroupFromUser::class)->handle($event->roleGroupPivot);
        // }

        // if ($event->roleGroupPivot->role_groupable_type === resolve(Role::class)->getMorphClass()) {
        //     resolve(RemoveRoleFromUsersInRoleGroup::class)->handle($event->roleGroupPivot);
        // }
    }
}

<?php

namespace Assist\Authorization\Listeners;

class HandleRoleRemovedFromUser
{
    public function handle(object $event): void
    {
        // Remove all role groups that this user may belong to that this role belonged to
        $event->role->roleGroups->each(function ($roleGroup) use ($event) {
            $event->user->roleGroups()->detach($roleGroup);
        });
    }
}

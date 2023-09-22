<?php

namespace Assist\Authorization\Listeners;

class HandleRoleRemovedFromUser
{
    public function handle(object $event): void
    {
        $event->role->roleGroups->each(function ($roleGroup) use ($event) {
            $event->user->roleGroups()->detach($roleGroup);
        });
    }
}

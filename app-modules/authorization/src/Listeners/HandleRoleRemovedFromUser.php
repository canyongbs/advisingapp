<?php

namespace Assist\Authorization\Listeners;

use Assist\Authorization\Enums\ModelHasRolesViaEnum;

class HandleRoleRemovedFromUser
{
    public function handle(object $event): void
    {
        if ($event->role->via === ModelHasRolesViaEnum::RoleGroup->value) {
            $event->role->roleGroups->each(function ($roleGroup) use ($event) {
                $event->user->roleGroups()->detach($roleGroup);
            });
        }
    }
}

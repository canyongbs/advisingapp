<?php

namespace Assist\Authorization\Listeners;

use Assist\Authorization\Actions\RemoveRoleFromUsersInRoleGroup;

class HandleRoleGroupRolePivotDeleted
{
    public function handle(object $event): void
    {
        ray('HandleRoleGroupRolePivotDeleted', $event);

        resolve(RemoveRoleFromUsersInRoleGroup::class)->handle($event->pivot);
    }
}

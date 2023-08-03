<?php

namespace Assist\Authorization\Listeners;

use Assist\Authorization\Actions\RemoveRoleFromUsersInRoleGroup;

class HandleRoleRemovedFromRoleGroup
{
    public function handle(object $event): void
    {
        ray('HandleRoleRemovedFromRoleGroup', $event);

        resolve(RemoveRoleFromUsersInRoleGroup::class)->handle($event->pivot);
    }
}

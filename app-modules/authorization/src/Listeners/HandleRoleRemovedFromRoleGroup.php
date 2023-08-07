<?php

namespace Assist\Authorization\Listeners;

use Assist\Authorization\Actions\RemoveRoleFromUsersInRoleGroup;

class HandleRoleRemovedFromRoleGroup
{
    public function handle(object $event): void
    {
        resolve(RemoveRoleFromUsersInRoleGroup::class)->handle($event->pivot);
    }
}

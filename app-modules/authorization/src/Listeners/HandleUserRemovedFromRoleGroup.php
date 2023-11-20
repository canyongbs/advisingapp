<?php

namespace Assist\Authorization\Listeners;

use Assist\Authorization\Actions\RemoveRolesForRoleGroupFromUser;

class HandleUserRemovedFromRoleGroup
{
    public function handle(object $event): void
    {
        resolve(RemoveRolesForRoleGroupFromUser::class)->handle($event->pivot);
    }
}

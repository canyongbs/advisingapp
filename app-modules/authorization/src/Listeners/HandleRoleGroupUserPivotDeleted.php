<?php

namespace Assist\Authorization\Listeners;

use Assist\Authorization\Actions\RemoveRolesForRoleGroupFromUser;

class HandleRoleGroupUserPivotDeleted
{
    public function handle(object $event): void
    {
        ray('HandleRoleGroupUserPivotDeleted', $event);

        resolve(RemoveRolesForRoleGroupFromUser::class)->handle($event->pivot);
    }
}

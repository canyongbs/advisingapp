<?php

namespace Assist\Authorization\Listeners;

use Assist\Authorization\Actions\AddRolesFromRoleGroupToUser;

class HandleRoleGroupUserPivotSaved
{
    public function handle(object $event): void
    {
        ray('HandleRoleGroupUserPivotSaved', $event);

        resolve(AddRolesFromRoleGroupToUser::class)->handle($event->pivot);
    }
}

<?php

namespace Assist\Authorization\Listeners;

use Assist\Authorization\Actions\AddNewRoleToExistingUsersInRoleGroup;

class HandleRoleAttachedToRoleGroup
{
    public function handle(object $event): void
    {
        ray('HandleRoleGroupRolePivotSaved', $event);

        resolve(AddNewRoleToExistingUsersInRoleGroup::class)->handle($event->pivot);
    }
}

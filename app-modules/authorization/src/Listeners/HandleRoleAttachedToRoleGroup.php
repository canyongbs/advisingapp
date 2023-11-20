<?php

namespace Assist\Authorization\Listeners;

use Assist\Authorization\Actions\AddNewRoleToExistingUsersInRoleGroup;

class HandleRoleAttachedToRoleGroup
{
    public function handle(object $event): void
    {
        resolve(AddNewRoleToExistingUsersInRoleGroup::class)->handle($event->pivot);
    }
}

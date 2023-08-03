<?php

namespace Assist\Authorization\Listeners;

use Assist\Authorization\Actions\AddRolesFromRoleGroupToUser;

class HandleUserAttachedToRoleGroup
{
    public function handle(object $event): void
    {
        ray('UserAttachedToRoleGroup', $event);

        resolve(AddRolesFromRoleGroupToUser::class)->handle($event->pivot);
    }
}

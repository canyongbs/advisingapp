<?php

namespace Assist\Authorization\Listeners;

use Assist\Authorization\Actions\AddRolesFromRoleGroupToUser;

class HandleUserAttachedToRoleGroup
{
    public function handle(object $event): void
    {
        resolve(AddRolesFromRoleGroupToUser::class)->handle($event->pivot);
    }
}

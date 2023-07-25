<?php

namespace App\Enums;

enum ModelHasRolesViaEnum: string
{
    case Direct = 'direct';

    case RoleGroup = 'role_group';
}

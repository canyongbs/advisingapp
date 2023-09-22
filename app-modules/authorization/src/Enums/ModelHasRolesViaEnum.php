<?php

namespace Assist\Authorization\Enums;

enum ModelHasRolesViaEnum: string
{
    case Direct = 'direct';

    case RoleGroup = 'role_group';
}

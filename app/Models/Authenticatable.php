<?php

namespace App\Models;

use App\Models\Concerns\CanOrElse;
use Assist\Authorization\Models\Concerns\HasRoleGroups;
use Assist\Authorization\Models\Concerns\HasRolesWithPivot;
use Illuminate\Foundation\Auth\User as BaseAuthenticatable;
use Assist\Authorization\Models\Concerns\DefinesPermissions;

abstract class Authenticatable extends BaseAuthenticatable
{
    use HasRoleGroups {
        HasRoleGroups::roleGroups as traitRoleGroups;
    }
    use HasRolesWithPivot;
    use DefinesPermissions;
    use CanOrElse;
}

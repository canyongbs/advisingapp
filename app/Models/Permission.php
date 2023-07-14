<?php

namespace App\Models;

use App\Models\Concerns\DefinesPermissions;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    use DefinesPermissions;
}

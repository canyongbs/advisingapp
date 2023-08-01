<?php

namespace Assist\Authorization\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Assist\Authorization\Events\RoleGroupRolePivotSaved;
use Assist\Authorization\Events\RoleGroupRolePivotDeleted;

class RoleGroupRolePivot extends Pivot
{
    protected $dispatchesEvents = [
        'saved' => RoleGroupRolePivotSaved::class,
        'deleted' => RoleGroupRolePivotDeleted::class,
    ];
}

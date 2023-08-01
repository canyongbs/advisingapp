<?php

namespace Assist\Authorization\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Assist\Authorization\Events\RoleGroupUserPivotSaved;
use Assist\Authorization\Events\RoleGroupUserPivotDeleted;

class RoleGroupUserPivot extends Pivot
{
    protected $dispatchesEvents = [
        'saved' => RoleGroupUserPivotSaved::class,
        'deleted' => RoleGroupUserPivotDeleted::class,
    ];
}

<?php

namespace Assist\Authorization\Models\Pivots;

use Assist\Authorization\Events\RoleGroupPivotSaved;
use Assist\Authorization\Events\RoleGroupPivotDeleted;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class RoleGroupPivot extends MorphPivot
{
    protected $dispatchesEvents = [
        'saved' => RoleGroupPivotSaved::class,
        'deleted' => RoleGroupPivotDeleted::class,
    ];
}

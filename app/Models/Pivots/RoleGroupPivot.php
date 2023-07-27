<?php

namespace App\Models\Pivots;

use App\Events\RoleGroupPivotSaved;
use App\Events\RoleGroupPivotDeleted;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class RoleGroupPivot extends MorphPivot
{
    protected $dispatchesEvents = [
        'saved' => RoleGroupPivotSaved::class,
        'deleted' => RoleGroupPivotDeleted::class,
    ];
}

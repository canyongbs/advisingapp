<?php

namespace Assist\Authorization\Models\Pivots;

use Assist\Authorization\Events\RoleGroupPivotSaved;
use Assist\Authorization\Events\RoleGroupPivotDeleted;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

/**
 * Assist\Authorization\Models\Pivots\RoleGroupPivot
 *
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroupPivot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroupPivot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroupPivot query()
 *
 * @mixin \Eloquent
 */
class RoleGroupPivot extends MorphPivot
{
    protected $dispatchesEvents = [
        'saved' => RoleGroupPivotSaved::class,
        'deleted' => RoleGroupPivotDeleted::class,
    ];
}

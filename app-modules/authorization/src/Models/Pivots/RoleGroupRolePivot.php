<?php

namespace Assist\Authorization\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Assist\Authorization\Events\RoleAttachedToRoleGroup;
use Assist\Authorization\Events\RoleRemovedFromRoleGroup;

/**
 * Assist\Authorization\Models\Pivots\RoleGroupRolePivot
 *
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroupRolePivot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroupRolePivot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroupRolePivot query()
 * @mixin \Eloquent
 * @mixin IdeHelperRoleGroupRolePivot
 */
class RoleGroupRolePivot extends Pivot
{
    protected $dispatchesEvents = [
        'saved' => RoleAttachedToRoleGroup::class,
        'deleted' => RoleRemovedFromRoleGroup::class,
    ];
}

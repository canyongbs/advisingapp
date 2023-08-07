<?php

namespace Assist\Authorization\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Assist\Authorization\Events\UserAttachedToRoleGroup;
use Assist\Authorization\Events\UserRemovedFromRoleGroup;

/**
 * Assist\Authorization\Models\Pivots\RoleGroupUserPivot
 *
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroupUserPivot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroupUserPivot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleGroupUserPivot query()
 *
 * @mixin \Eloquent
 */
class RoleGroupUserPivot extends Pivot
{
    protected $dispatchesEvents = [
        'saved' => UserAttachedToRoleGroup::class,
        'deleted' => UserRemovedFromRoleGroup::class,
    ];
}

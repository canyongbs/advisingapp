<?php

namespace Assist\Authorization\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Assist\Authorization\Events\RoleAttachedToRoleGroup;
use Assist\Authorization\Events\RoleRemovedFromRoleGroup;

class RoleGroupRolePivot extends Pivot
{
    protected $dispatchesEvents = [
        'saved' => RoleAttachedToRoleGroup::class,
        'deleted' => RoleRemovedFromRoleGroup::class,
    ];
}

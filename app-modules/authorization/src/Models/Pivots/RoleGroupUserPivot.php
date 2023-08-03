<?php

namespace Assist\Authorization\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Assist\Authorization\Events\UserAttachedToRoleGroup;
use Assist\Authorization\Events\UserRemovedFromRoleGroup;

class RoleGroupUserPivot extends Pivot
{
    protected $dispatchesEvents = [
        'saved' => UserAttachedToRoleGroup::class,
        'deleted' => UserRemovedFromRoleGroup::class,
    ];
}

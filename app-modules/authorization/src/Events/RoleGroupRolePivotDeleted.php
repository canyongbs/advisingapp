<?php

namespace Assist\Authorization\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Assist\Authorization\Models\Pivots\RoleGroupRolePivot;

class RoleGroupRolePivotDeleted
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public RoleGroupRolePivot $pivot
    ) {
    }
}

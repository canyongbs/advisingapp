<?php

namespace Assist\Authorization\Events;

use App\Models\User;
use Assist\Authorization\Models\Role;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class RoleRemovedFromUser
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Role $role,
        public User $user
    ) {}
}

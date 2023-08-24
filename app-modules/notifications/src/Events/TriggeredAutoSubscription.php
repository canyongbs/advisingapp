<?php

namespace Assist\Notifications\Events;

use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Assist\Notifications\Models\Contracts\Subscribable;

class TriggeredAutoSubscription
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public User $user,
        public Subscribable $subscribable,
    ) {}
}

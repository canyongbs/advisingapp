<?php

namespace Assist\Notifications\Events;

use Illuminate\Queue\SerializesModels;
use Assist\Notifications\Models\Subscription;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class SubscriptionDeleted
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public Subscription $subscription
    ) {}
}

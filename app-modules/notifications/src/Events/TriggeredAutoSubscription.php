<?php

namespace Assist\Notifications\Events;

use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Assist\Notifications\Models\Contracts\Subscribable;
use Assist\Notifications\Models\Contracts\CanTriggerAutoSubscription;

class TriggeredAutoSubscription
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public ?Subscribable $subscribable;

    public function __construct(
        public User $user,
        public CanTriggerAutoSubscription $model,
    ) {
        $this->subscribable = $model->getSubscribable();
    }
}

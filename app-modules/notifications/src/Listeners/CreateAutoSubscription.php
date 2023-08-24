<?php

namespace Assist\Notifications\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Assist\Notifications\Actions\SubscriptionCreate;
use Assist\Notifications\Events\TriggeredAutoSubscription;

class CreateAutoSubscription implements ShouldQueue
{
    public function handle(TriggeredAutoSubscription $event): void
    {
        if (empty($event->subscribable) || empty($event->user)) {
            return;
        }

        resolve(SubscriptionCreate::class)->handle($event->user, $event->subscribable, false);
    }
}

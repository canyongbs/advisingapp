<?php

namespace Assist\Notifications\Listeners;

use Assist\Notifications\Actions\SubscriptionCreate;
use Assist\Notifications\Events\TriggeredAutoSubscription;

class CreateAutoSubscription
{
    public function handle(TriggeredAutoSubscription $event): void
    {
        resolve(SubscriptionCreate::class)->handle($event->user, $event->subscribable, false);
    }
}

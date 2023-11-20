<?php

namespace Assist\Notifications\Observers;

use Assist\Notifications\Models\Subscription;
use Assist\Notifications\Events\SubscriptionCreated;
use Assist\Notifications\Events\SubscriptionDeleted;

class SubscriptionObserver
{
    public function created(Subscription $subscription): void
    {
        SubscriptionCreated::dispatch($subscription);
    }

    public function deleted(Subscription $subscription): void
    {
        SubscriptionDeleted::dispatch($subscription);
    }
}

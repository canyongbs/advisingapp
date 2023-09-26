<?php

namespace Assist\Notifications\Listeners;

use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Assist\Notifications\Events\SubscriptionCreated;

class NotifyUserOfSubscriptionCreated implements ShouldQueue
{
    public function handle(SubscriptionCreated $event): void
    {
        Notification::make()
            ->status('success')
            ->title("You have been subscribed to {$event->subscription->subscribable->getSubscriptionDisplayName()}")
            ->sendToDatabase($event->subscription->user);
    }
}

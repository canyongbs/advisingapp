<?php

namespace Assist\Notifications\Listeners;

use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Assist\Notifications\Events\SubscriptionCreated;

class NotifyUserOfSubscriptionCreated implements ShouldQueue
{
    public function handle(SubscriptionCreated $event): void
    {
        $name = $event->subscription->subscribable->{$event->subscription->subscribable->displayNameKey()};

        Notification::make()
            ->status('success')
            ->title("You have been subscribed to {$name}")
            ->sendToDatabase($event->subscription->user);
    }
}

<?php

namespace Assist\Notifications\Listeners;

use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Assist\Notifications\Events\SubscriptionDeleted;

class NotifyUserOfSubscriptionDeleted implements ShouldQueue
{
    public function handle(SubscriptionDeleted $event): void
    {
        $name = $event->subscription->subscribable->{$event->subscription->subscribable->displayNameKey()};

        Notification::make()
            ->status('warning')
            ->title("You have been unsubscribed from {$name}")
            ->sendToDatabase($event->subscription->user);
    }
}

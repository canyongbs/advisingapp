<?php

namespace Assist\Notifications\Listeners;

use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Assist\Notifications\Events\SubscriptionDeleted;

class NotifyUserOfSubscriptionDeleted implements ShouldQueue
{
    public function handle(SubscriptionDeleted $event): void
    {
        Notification::make()
            ->status('warning')
            ->title("You have been unsubscribed from {$event->subscription->subscribable->full}")
            ->sendToDatabase($event->subscription->user);
    }
}

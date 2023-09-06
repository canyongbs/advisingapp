<?php

namespace Assist\Engagement\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Events\NotificationFailed;
use Assist\Engagement\Notifications\EngagementNotification;

// TODO I might want to change these to be scoped to Engagement a little better
class HandleEngagementNotificationFailed implements ShouldQueue
{
    public function handle(NotificationFailed $event): void
    {
        if (! $event->notification instanceof EngagementNotification) {
            return;
        }

        /** @var EngagementNotification $notification */
        $notification = $event->notification;

        /** @var EngagementDeliverable $deliverable */
        $deliverable = $notification->deliverable;

        $deliverable->markDeliveryFailed();
    }
}

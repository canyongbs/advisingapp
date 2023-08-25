<?php

namespace Assist\Engagement\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Events\NotificationSent;
use Assist\Engagement\Notifications\EngagementNotification;

class HandleNotificationSent implements ShouldQueue
{
    public function handle(NotificationSent $event): void
    {
        if (! $event->notification instanceof EngagementNotification) {
            return;
        }

        /** @var EngagementNotification $notification */
        $notification = $event->notification;

        /** @var EngagementDeliverable $deliverable */
        $deliverable = $notification->deliverable;

        $deliverable->markDeliverySuccessful();
    }
}

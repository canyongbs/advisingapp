<?php

namespace Assist\Engagement\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Events\NotificationSent;
use Assist\Engagement\Notifications\EngagementNotification;
use Assist\Engagement\Notifications\EngagementEmailSentNotification;

// TODO Turn this into a queued job once generic listener is in place
class HandleEngagementNotificationSent implements ShouldQueue
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

        if (is_null($deliverable->engagement->engagement_batch_id)) {
            $deliverable->engagement->user->notify(new EngagementEmailSentNotification($deliverable->engagement));
        }
    }
}

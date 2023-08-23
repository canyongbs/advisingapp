<?php

namespace Assist\Engagement\Actions;

use Assist\Engagement\Notifications\EngagementNotification;

class EngagementEmailChannelDelivery extends QueuedEngagementDelivery
{
    public function deliver(): void
    {
        $this
            ->deliverable
            ->engagement
            ->recipient
            ->notify(new EngagementNotification($this->deliverable));

        // TODO We need to ensure that the notification was successfully delivered
        // Before marking this as such
        // This can probably be extracted into the QueuedEngagementDelivery,
        // as we can figure out how to return the result of the `deliver()`
        // Method and process the results there...
        $this->deliverable->markDeliverySuccessful();
    }
}

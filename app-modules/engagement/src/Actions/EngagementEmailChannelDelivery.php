<?php

namespace Assist\Engagement\Actions;

use Assist\Engagement\Notifications\EngagementNotification;

class EngagementEmailChannelDelivery extends QueuedEngagementDelivery
{
    public function deliver(): void
    {
        // TODO Remove this... It's simply for testing
        sleep(10);

        $this
            ->deliverable
            ->engagement
            ->recipient
            ->notify(new EngagementNotification($this->deliverable));
    }
}

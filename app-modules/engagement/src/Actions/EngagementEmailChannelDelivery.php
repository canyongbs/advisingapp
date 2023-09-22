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
    }
}

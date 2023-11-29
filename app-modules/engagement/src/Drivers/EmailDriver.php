<?php

namespace Assist\Engagement\Handlers;

use Assist\Engagement\Models\EngagementDeliverable;

class EmailDriver implements DeliverableDriver
{
    public function __construct(
        protected EngagementDeliverable $deliverable
    ) {}

    public function updateDeliveryStatus(array $data): void {}
}

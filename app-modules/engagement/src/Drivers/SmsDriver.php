<?php

namespace Assist\Engagement\Handlers;

use Assist\Engagement\Models\EngagementDeliverable;

class SmsDriver implements DeliverableDriver
{
    public function __construct(
        protected EngagementDeliverable $deliverable
    ) {}

    public function updateDeliveryStatus(array $data): void
    {
        $this->deliverable->update([
            'external_status' => $data['MessageStatus'] ?? null,
        ]);

        match ($this->deliverable->external_status) {
            'delivered', 'read' => $this->deliverable->markDeliverySuccessful(),
            'undelivered', 'failed' => $this->deliverable->markDeliveryFailed($data['ErrorMessage'] ?? null),
            default => null,
        };
    }
}

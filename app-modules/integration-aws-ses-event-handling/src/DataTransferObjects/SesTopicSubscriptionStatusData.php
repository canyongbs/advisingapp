<?php

namespace Assist\IntegrationAwsSesEventHandling\DataTransferObjects;

use Spatie\LaravelData\Data;

class SesTopicSubscriptionStatusData extends Data
{
    public function __construct(
        public string $topicName,
        public string $subscriptionStatus,
    ) {}
}

<?php

namespace Assist\IntegrationAwsSesEventHandling\DataTransferObjects;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Attributes\DataCollectionOf;

class SesTopicPreferencesData extends Data
{
    public function __construct(
        public string $unsubscribeAll,
        #[DataCollectionOf(SesTopicSubscriptionStatusData::class)]
        public DataCollection $topicSubscriptionStatus,
        #[DataCollectionOf(SesTopicSubscriptionStatusData::class)]
        public DataCollection|Optional $topicDefaultSubscriptionStatus,
    ) {}
}

<?php

namespace Assist\IntegrationAwsSesEventHandling\DataTransferObjects;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Attributes\DataCollectionOf;

class SesSubscriptionData extends Data
{
    public function __construct(
        public string $contactList,
        public string $timestamp,
        public string $source,
        #[DataCollectionOf(SesTopicPreferencesData::class)]
        public DataCollection $newTopicPreferences,
        #[DataCollectionOf(SesTopicPreferencesData::class)]
        public DataCollection $oldTopicPreferences,
    ) {}
}

<?php

namespace Assist\IntegrationAwsSesEventHandling\DataTransferObjects;

use Spatie\LaravelData\Data;

class SesSubscriptionData extends Data
{
    public function __construct(
        public string $contactList,
        public string $timestamp,
        public string $source,
        public SesTopicPreferencesData $newTopicPreferences,
        public SesTopicPreferencesData $oldTopicPreferences,
    ) {}
}

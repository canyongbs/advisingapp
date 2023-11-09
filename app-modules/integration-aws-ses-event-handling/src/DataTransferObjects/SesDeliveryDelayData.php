<?php

namespace Assist\IntegrationAwsSesEventHandling\DataTransferObjects;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Attributes\DataCollectionOf;

class SesDeliveryDelayData extends Data
{
    public function __construct(
        public string $delayType,
        #[DataCollectionOf(SesDelayedRecipientsData::class)]
        public DataCollection $delayedRecipients,
        public string $expirationTime,
        public string|Optional $reportingMTA,
        public string $timestamp,
    ) {}
}

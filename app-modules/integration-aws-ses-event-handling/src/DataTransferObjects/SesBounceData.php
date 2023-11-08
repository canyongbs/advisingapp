<?php

namespace Assist\IntegrationAwsSesEventHandling\DataTransferObjects;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Attributes\DataCollectionOf;

class SesBounceData extends Data
{
    public function __construct(
        public string $bounceType,
        public string $bounceSubType,
        #[DataCollectionOf(SesBouncedRecipients::class)]
        public DataCollection $bouncedRecipients,
        public string $timestamp,
        public string $feedbackId,
        public string|Optional $reportingMta,
    ) {}
}

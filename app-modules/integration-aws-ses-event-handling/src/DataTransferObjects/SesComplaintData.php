<?php

namespace Assist\IntegrationAwsSesEventHandling\DataTransferObjects;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Attributes\DataCollectionOf;

class SesComplaintData extends Data
{
    public function __construct(
        #[DataCollectionOf(ComplainedRecipientsData::class)]
        public DataCollection $complainedRecipients,
        public string $timestamp,
        public string $feedbackId,
        public string $complaintSubType,
        public string|Optional $userAgent,
        public string|Optional $complaintFeedbackType,
        public string|Optional $arrivalDate,
    ) {}
}

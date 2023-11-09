<?php

namespace Assist\IntegrationAwsSesEventHandling\DataTransferObjects;

use Spatie\LaravelData\Data;

class SesDelayedRecipientsData extends Data
{
    public function __construct(
        public string $emailAddress,
        public string $status,
        public string $diagnosticCode,
    ) {}
}

<?php

namespace Assist\IntegrationAwsSesEventHandling\DataTransferObjects;

use Spatie\LaravelData\Data;

class ComplainedRecipientsData extends Data
{
    public function __construct(
        public string $emailAddress,
    ) {}
}

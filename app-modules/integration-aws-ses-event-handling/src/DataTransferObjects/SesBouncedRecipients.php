<?php

namespace Assist\IntegrationAwsSesEventHandling\DataTransferObjects;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class SesBouncedRecipients extends Data
{
    public function __construct(
        public string $emailAddress,
        public string|Optional $action,
        public string|Optional $status,
        public string|Optional $diagnosticCode,
    ) {}
}

<?php

namespace Assist\IntegrationAwsSesEventHandling\DataTransferObjects;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class SesDeliveryData extends Data
{
    public function __construct(
        public string $timestamp,
        public int $processingTimeMillis,
        public array $recipients,
        public string $smtpResponse,
        public string|Optional $reportingMta,
    ) {}
}

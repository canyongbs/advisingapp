<?php

namespace Assist\IntegrationAwsSesEventHandling\DataTransferObjects;

use Spatie\LaravelData\Data;

class SesDeliveryData extends Data {
    public function __construct(
        public string $timestamp,
        public int $processingTimeMillis,
        public array $recipients,
        public string $smtpResponse,
        public string $reportingMta,
    ) {}
}

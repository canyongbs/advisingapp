<?php

namespace Assist\IntegrationAwsSesEventHandling\DataTransferObjects;

use Spatie\LaravelData\Data;

class SesOpenData extends Data
{
    public function __construct(
        public string $ipAddress,
        public string $timestamp,
        public string $userAgent,
    ) {}
}

<?php

namespace Assist\IntegrationAwsSesEventHandling\DataTransferObjects;

use Spatie\LaravelData\Data;

class SesRejectData extends Data
{
    public function __construct(
        public string $reason,
    ) {}
}

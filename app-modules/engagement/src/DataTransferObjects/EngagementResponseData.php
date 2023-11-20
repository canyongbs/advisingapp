<?php

namespace Assist\Engagement\DataTransferObjects;

use Spatie\LaravelData\Data;

class EngagementResponseData extends Data
{
    public function __construct(
        public string $from,
        public string $body,
    ) {}
}

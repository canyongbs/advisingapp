<?php

namespace Assist\IntegrationAwsSesEventHandling\DataTransferObjects;

use Spatie\LaravelData\Data;

class SesClickData extends Data
{
    public function __construct(
        public string $ipAddress,
        public string $timestamp,
        public string $userAgent,
        public string $link,
        public array $linkTags,
    ) {}
}

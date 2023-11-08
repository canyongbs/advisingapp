<?php

namespace Assist\IntegrationAwsSesEventHandling\DataTransferObjects;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class SesMailData extends Data
{
    public function __construct(
        public string $timestamp,
        public string $messageId,
        public string $source,
        public mixed $sourceArn,
        public string $sendingAccountId,
        public array $destination,
        public bool $headersTruncated,
        public array|Optional $headers,
        public array|Optional $commonHeaders,
        public array $tags,
    ) {}
}

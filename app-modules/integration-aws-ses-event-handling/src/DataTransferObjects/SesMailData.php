<?php

namespace Assist\IntegrationAwsSesEventHandling\DataTransferObjects;

use Spatie\LaravelData\Data;

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
        public array $headers,
        public array $commonHeaders,
        public array $tags,
    ) {}
}

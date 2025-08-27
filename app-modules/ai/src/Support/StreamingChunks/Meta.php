<?php

namespace AdvisingApp\Ai\Support\StreamingChunks;

readonly class Meta
{
    /**
     * @param array<string, mixed> $nextRequestOptions
     */
    public function __construct(
        public ?string $messageId,
        public array $nextRequestOptions,
    ) {}
}

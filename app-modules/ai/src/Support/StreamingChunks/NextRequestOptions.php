<?php

namespace AdvisingApp\Ai\Support\StreamingChunks;

readonly class NextRequestOptions
{
    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        public array $options,
    ) {}
}

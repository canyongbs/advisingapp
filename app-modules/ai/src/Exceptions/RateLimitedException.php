<?php

namespace AdvisingApp\Ai\Exceptions;

use Exception;

class RateLimitedException extends Exception
{
    public int $retryAfterSeconds;

    public function __construct(int $retryAfterSeconds)
    {
        $this->retryAfterSeconds = $retryAfterSeconds;

        parent::__construct('Heavy traffic, just a few more moments...');
    }
}

<?php

namespace Assist\IntegrationTwilio\DataTransferObjects;

use Spatie\LaravelData\Data;

class TwilioApiKey extends Data
{
    public function __construct(
        public string $api_sid,
        public string $secret,
    ) {}
}

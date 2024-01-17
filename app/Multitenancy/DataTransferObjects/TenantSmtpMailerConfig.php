<?php

namespace App\Multitenancy\DataTransferObjects;

use Spatie\LaravelData\Data;

class TenantSmtpMailerConfig extends Data
{
    public function __construct(
        public ?string $host = null,
        public int $port = 587,
        public ?string $encryption = null,
        public ?string $username = null,
        public ?string $password = null,
        public ?int $timeout = null,
        public ?string $localDomain = null,
    ) {}
}

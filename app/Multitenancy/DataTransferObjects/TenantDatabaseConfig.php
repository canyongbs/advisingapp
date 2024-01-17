<?php

namespace App\Multitenancy\DataTransferObjects;

use Spatie\LaravelData\Data;

class TenantDatabaseConfig extends Data
{
    public function __construct(
        public ?string $host = null,
        public ?string $port = null,
        public ?string $database = null,
        public ?string $username = null,
        public ?string $password = null,
    ) {}
}

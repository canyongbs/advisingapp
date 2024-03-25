<?php

namespace App\Multitenancy\DataTransferObjects;

use Spatie\LaravelData\Data;

class TenantUser extends Data
{
    public function __construct(
        public ?string $name = null,
        public ?string $email = null,
        public ?string $password = null,
    ) {}
}

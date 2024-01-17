<?php

namespace App\Multitenancy\DataTransferObjects;

use Spatie\LaravelData\Data;

class TenantMailConfig extends Data
{
    public function __construct(
        public TenantMailersConfig $mailers,
        public string $mailer = 'smtp',
        public string $fromAddress = 'hello@example.com',
        public string $fromName = 'Example',
    ) {}
}

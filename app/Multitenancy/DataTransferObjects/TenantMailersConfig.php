<?php

namespace App\Multitenancy\DataTransferObjects;

use Spatie\LaravelData\Data;

class TenantMailersConfig extends Data
{
    public function __construct(
        public TenantSmtpMailerConfig $smtp,
    ) {}
}

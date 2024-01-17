<?php

namespace App\Multitenancy\DataTransferObjects;

use Spatie\LaravelData\Data;

class TenantS3FilesystemConfig extends Data
{
    public function __construct(
        public ?string $key = null,
        public ?string $secret = null,
        public ?string $region = null,
        public ?string $bucket = null,
        public ?string $url = null,
        public ?string $endpoint = null,
        public bool $usePathStyleEndpoint = false,
        public bool $throw = false,
        public ?string $root = null,
    ) {}
}

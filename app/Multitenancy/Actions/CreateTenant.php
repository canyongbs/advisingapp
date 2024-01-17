<?php

namespace App\Multitenancy\Actions;

use App\Models\Tenant;
use Illuminate\Encryption\Encrypter;
use App\Multitenancy\DataTransferObjects\TenantConfig;

class CreateTenant
{
    public function __invoke(
        string $name,
        string $domain,
        TenantConfig $config,
    ): Tenant {
        return Tenant::query()
            ->create(
                [
                    'name' => $name,
                    'domain' => $domain,
                    'key' => $this->generateTenantKey(),
                    'config' => $config,
                ]
            );
    }

    protected function generateTenantKey(): string
    {
        return 'base64:' . base64_encode(
            Encrypter::generateKey(config('app.cipher'))
        );
    }
}

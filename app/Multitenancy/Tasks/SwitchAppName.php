<?php

namespace App\Multitenancy\Tasks;

use Spatie\Multitenancy\Models\Tenant;
use Spatie\Multitenancy\Tasks\SwitchTenantTask;
use App\Multitenancy\DataTransferObjects\TenantConfig;

class SwitchAppName implements SwitchTenantTask
{
    public function __construct(
        protected ?string $originalAppName = null,
    ) {
        $this->originalAppName ??= config('app.name');
    }

    public function makeCurrent(Tenant $tenant): void
    {
        /** @var TenantConfig $config */
        $config = $tenant->config;

        $appName = $config->applicationName ?? config('app.name');

        $this->setAppName($appName);
    }

    public function forgetCurrent(): void
    {
        $this->setAppName($this->originalAppName);
    }

    protected function setAppName(string $appName): void
    {
        config(['app.name' => $appName]);
    }
}

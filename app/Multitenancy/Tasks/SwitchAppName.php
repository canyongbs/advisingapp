<?php

namespace App\Multitenancy\Tasks;

use Spatie\Multitenancy\Models\Tenant;
use Spatie\Multitenancy\Tasks\SwitchTenantTask;

class SwitchAppName implements SwitchTenantTask
{
    public function __construct(
        protected ?string $originalAppName = null,
    ) {
        $this->originalAppName ??= config('app.name');
    }

    public function makeCurrent(Tenant $tenant): void
    {
        $this->setAppName("{$tenant->name} | {$this->originalAppName}");
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

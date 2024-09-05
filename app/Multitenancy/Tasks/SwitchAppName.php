<?php

namespace App\Multitenancy\Tasks;

use Spatie\Multitenancy\Models\Tenant;
use AdvisingApp\Theme\Settings\ThemeSettings;
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
        $appName = app(ThemeSettings::class)->application_name;
        $appName = ! blank($appName) ? $appName : config('app.name');
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

<?php

namespace App\Multitenancy\Tasks;

use Spatie\Multitenancy\Models\Tenant;
use Spatie\Multitenancy\Tasks\SwitchTenantTask;

class SwitchAppUrl implements SwitchTenantTask
{
    public function __construct(
        protected ?string $originalUrl = null,
    ) {
        $this->originalUrl ??= config('app.url');
    }

    public function makeCurrent(Tenant $tenant): void
    {
        // We may want to look into defining whether we want to use https at the tenant level
        $scheme = parse_url(config('app.url'))['scheme'];

        $this->setAppUrl("{$scheme}://{$tenant->domain}");
    }

    public function forgetCurrent(): void
    {
        $this->setAppUrl($this->originalUrl);
    }

    protected function setAppUrl(string $url)
    {
        config([
            'app.url' => $url,
        ]);
    }
}

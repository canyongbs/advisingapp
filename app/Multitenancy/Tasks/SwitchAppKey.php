<?php

namespace App\Multitenancy\Tasks;

use App\Actions\ChangeAppKey;
use Spatie\Multitenancy\Models\Tenant;
use Spatie\Multitenancy\Tasks\SwitchTenantTask;

class SwitchAppKey implements SwitchTenantTask
{
    public function __construct(
        protected ?string $originalAppKey = null,
    ) {
        $this->originalAppKey ??= config('app.key');
    }

    public function makeCurrent(Tenant $tenant): void
    {
        $this->setAppKey($tenant->key);
    }

    public function forgetCurrent(): void
    {
        $this->setAppKey($this->originalAppKey);
    }

    protected function setAppKey(string $appKey): void
    {
        app(ChangeAppKey::class)($appKey);
    }
}

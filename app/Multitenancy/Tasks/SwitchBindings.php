<?php

namespace App\Multitenancy\Tasks;

use App\Listeners\HandleSettingsSaved;
use Spatie\Multitenancy\Models\Tenant;
use App\Listeners\HandleSettingsSavedForTenant;
use Spatie\Multitenancy\Tasks\SwitchTenantTask;
use App\Listeners\Contracts\HandleSettingsSaved as HandleSettingsSavedContract;

class SwitchBindings implements SwitchTenantTask
{
    public function makeCurrent(Tenant $tenant): void
    {
        app()->bind(HandleSettingsSavedContract::class, fn () => new HandleSettingsSavedForTenant());
    }

    public function forgetCurrent(): void
    {
        app()->bind(HandleSettingsSavedContract::class, fn () => new HandleSettingsSaved());
    }
}

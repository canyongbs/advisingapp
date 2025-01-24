<?php

namespace App\Listeners;

use App\Multitenancy\Events\NewTenantSetupComplete;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Artisan;
use Spatie\Multitenancy\Jobs\NotTenantAware;
use Spatie\ScheduleMonitor\Commands\SyncCommand;

class SyncScheduleMonitor implements ShouldQueue, NotTenantAware
{
    public function handle(NewTenantSetupComplete $event): void
    {
        Artisan::call(SyncCommand::class);
    }
}

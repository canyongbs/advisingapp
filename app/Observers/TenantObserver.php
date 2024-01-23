<?php

namespace App\Observers;

use Throwable;
use App\Models\Tenant;
use Illuminate\Bus\Batch;
use App\Jobs\MigrateTenantDatabase;
use App\Jobs\SetupMaterializedView;
use Illuminate\Support\Facades\Bus;
use App\Jobs\SetupForeignDataWrapper;
use Illuminate\Support\Facades\Event;
use App\Jobs\MigrateTenantSisDatabase;
use App\Multitenancy\Events\NewTenantSetupFailure;
use App\Multitenancy\Events\NewTenantSetupComplete;

class TenantObserver
{
    public function created(Tenant $tenant): void
    {
        $jobChain = app()->environment('local')
            ? [
                new MigrateTenantSisDatabase($tenant),
            ]
            : [];

        Bus::batch(
            [
                [
                    ...$jobChain,
                    new MigrateTenantDatabase($tenant),
                    new SetupForeignDataWrapper($tenant),
                    new SetupMaterializedView($tenant),
                ],
            ]
        )
            ->onQueue('landlord')
            ->then(function (Batch $batch) use ($tenant) {
                Event::dispatch(new NewTenantSetupComplete($tenant));
            })
            ->catch(function (Batch $batch, Throwable $e) use ($tenant) {
                Event::dispatch(new NewTenantSetupFailure($tenant, $e));
            })
            ->allowFailures()
            ->dispatch();
    }
}

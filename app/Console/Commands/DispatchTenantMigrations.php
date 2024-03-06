<?php

namespace App\Console\Commands;

use Throwable;
use App\Models\Tenant;
use Illuminate\Bus\Batch;
use App\Jobs\TenantSchemaMigration;
use Illuminate\Support\Facades\Bus;
use App\Jobs\DispatchTenantDataMigrations;
use App\Events\TenantMigrationBatchFailure;
use App\Events\TenantMigrationBatchSuccessful;
use Spatie\Multitenancy\Commands\Concerns\TenantAware;
use Symfony\Component\Console\Command\Command as CommandAlias;

class DispatchTenantMigrations extends DispatchMigrations
{
    use TenantAware;

    protected $signature = 'app:dispatch-tenant-migrations {--tenant=*}';

    protected $description = 'Dispatches Tenant schema and data migrations.';

    public function handle(): int
    {
        /** @var Tenant $tenant */
        $tenant = Tenant::current();

        Bus::batch(
            [
                [
                    new TenantSchemaMigration(),
                    new DispatchTenantDataMigrations(),
                ],
            ]
        )
            ->name("tenant-migrations-{$tenant->getKey()}-" . $this->getVersionTag())
            ->catch(function (Batch $batch, Throwable $throwable) use ($tenant) {
                event(new TenantMigrationBatchFailure(
                    batch: $batch,
                    tenant: $tenant,
                    throwable: $throwable
                ));
            })
            ->then(function (Batch $batch) use ($tenant) {
                event(new TenantMigrationBatchSuccessful(
                    batch: $batch,
                    tenant: $tenant,
                ));
            })
            ->dispatch();

        return CommandAlias::SUCCESS;
    }
}

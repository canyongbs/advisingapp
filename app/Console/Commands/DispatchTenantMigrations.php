<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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

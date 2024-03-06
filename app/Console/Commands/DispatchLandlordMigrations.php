<?php

namespace App\Console\Commands;

use Throwable;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use App\Jobs\LandlordSchemaMigration;
use App\Jobs\DispatchLandlordDataMigrations;
use App\Events\LandlordMigrationBatchFailure;
use App\Events\LandlordMigrationBatchSuccessful;
use Symfony\Component\Console\Command\Command as CommandAlias;

class DispatchLandlordMigrations extends DispatchMigrations
{
    protected $signature = 'app:dispatch-landlord-migrations';

    protected $description = 'Dispatches Landlord schema and data migrations.';

    public function handle(): int
    {
        Bus::batch(
            [
                [
                    new LandlordSchemaMigration(),
                    new DispatchLandlordDataMigrations(),
                ],
            ]
        )
            ->name('landlord-migrations-' . $this->getVersionTag())
            ->catch(function (Batch $batch, Throwable $e) {
                event(new LandlordMigrationBatchFailure($batch, $e));
            })
            ->then(function (Batch $batch) {
                event(new LandlordMigrationBatchSuccessful($batch));
            })
            ->dispatch();

        return CommandAlias::SUCCESS;
    }
}

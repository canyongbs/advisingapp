<?php

namespace App\Jobs;

use DateTime;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\Multitenancy\Jobs\NotTenantAware;
use AdvisingApp\DataMigration\Models\Operation;
use AdvisingApp\DataMigration\Enums\OperationType;
use AdvisingApp\DataMigration\OneTimeOperationFile;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use AdvisingApp\DataMigration\OneTimeOperationManager;
use AdvisingApp\DataMigration\Jobs\LandlordOneTimeOperationProcessJob;

class DispatchLandlordDataMigrations implements ShouldQueue, NotTenantAware
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use Batchable;

    public function __construct() {}

    public function retryUntil(): DateTime
    {
        return now()->addMinutes(2);
    }

    public function middleware(): array
    {
        return [
            (new WithoutOverlapping())
                ->releaseAfter(30)
                ->expireAfter(300),
        ];
    }

    public function handle(): void
    {
        try {
            DB::beginTransaction();

            $jobs = OneTimeOperationManager::getUnprocessedOperationFiles()
                ->filter(function (OneTimeOperationFile $operationFile) {
                    return $operationFile->getClassObject()->getType() == OperationType::Landlord;
                })
                ->filter(function (OneTimeOperationFile $operationFile) {
                    return $operationFile->getClassObject()->getTag() == 'after-deployment';
                })
                ->map(function (OneTimeOperationFile $operationFile) {
                    $operation = Operation::storeOperation($operationFile->getOperationName(), true);

                    return new LandlordOneTimeOperationProcessJob($operationFile->getOperationName(), $operation);
                });

            $this->batch()->add($jobs->toArray());

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }
}

<?php

namespace App\Jobs;

use DateTime;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\Multitenancy\Jobs\NotTenantAware;
use Illuminate\Queue\Middleware\WithoutOverlapping;

class LandlordSchemaMigration implements ShouldQueue, NotTenantAware
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use Batchable;

    public int $maxExceptions = 2;

    public function __construct() {}

    public function retryUntil(): DateTime
    {
        return now()->addMinutes(30);
    }

    public function middleware(): array
    {
        return [
            (new WithoutOverlapping())
                ->releaseAfter(60)
                ->expireAfter(60 * 30),
        ];
    }

    public function handle(): void
    {
        $currentQueueFailedConnection = config('queue.failed.database');

        config(['queue.failed.database' => 'landlord']);

        Artisan::call(
            command: 'migrate --database=landlord --path=database/landlord --force --isolated'
        );

        config(['queue.failed.database' => $currentQueueFailedConnection]);

        Log::info('Landlord schema migration finished', [
            'output' => Artisan::output(),
        ]);
    }
}

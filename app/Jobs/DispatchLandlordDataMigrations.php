<?php

namespace App\Jobs;

use DateTime;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\Multitenancy\Jobs\NotTenantAware;
use Illuminate\Queue\Middleware\WithoutOverlapping;

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
        return now()->addMinutes(30);
    }

    public function middleware(): array
    {
        // TODO: expireAfter?
        return [
            (new WithoutOverlapping())
                ->releaseAfter(60),
        ];
    }

    public function handle(): void {}
}

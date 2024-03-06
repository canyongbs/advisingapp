<?php

namespace App\Jobs;

use DateTime;
use App\Models\Tenant;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\Middleware\WithoutOverlapping;

class TenantSchemaMigration implements ShouldQueue
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
            (new WithoutOverlapping(Tenant::current()->getKey()))
                ->releaseAfter(60)
                ->expireAfter(60 * 30),
        ];
    }

    public function handle(): void
    {
        Artisan::call(
            command: 'migrate --database=tenant --force --isolated'
        );

        Log::info('Tenant schema migration finished', [
            'output' => Artisan::output(),
        ]);
    }
}

<?php

namespace App\Jobs;

use App\Models\Tenant;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Spatie\Multitenancy\Jobs\NotTenantAware;
use App\Console\Commands\CreateAdmMaterializedViews;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;

class SetupMaterializedView implements ShouldQueue, NotTenantAware
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public Tenant $tenant) {}

    public function middleware(): array
    {
        return [new SkipIfBatchCancelled()];
    }

    public function handle(): void
    {
        $this->tenant->execute(function () {
            Artisan::call(
                command: CreateAdmMaterializedViews::class,
                parameters: [
                    '--tenant' => $this->tenant->id,
                ],
            );
        });
    }
}

<?php

namespace App\Events;

use Throwable;
use App\Models\Tenant;
use Illuminate\Bus\Batch;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class TenantMigrationBatchFailure
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        protected Batch $batch,
        protected Tenant $tenant,
        protected Throwable $throwable,
    ) {}
}

<?php

namespace App\Events;

use App\Models\Tenant;
use Illuminate\Bus\Batch;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class TenantMigrationBatchSuccessful
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        protected Batch $batch,
        protected Tenant $tenant,
    ) {}
}

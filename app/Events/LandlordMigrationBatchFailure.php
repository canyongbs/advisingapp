<?php

namespace App\Events;

use Throwable;
use Illuminate\Bus\Batch;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class LandlordMigrationBatchFailure
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        protected Batch $batch,
        protected Throwable $e,
    ) {}
}

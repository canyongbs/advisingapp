<?php

namespace App\Events;

use Illuminate\Bus\Batch;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;

class LandlordMigrationBatchSuccessful
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(protected Batch $batch) {}
}

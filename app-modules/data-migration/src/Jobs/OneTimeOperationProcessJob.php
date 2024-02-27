<?php

namespace AdvisingApp\DataMigration\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use AdvisingApp\DataMigration\Models\Operation;
use AdvisingApp\DataMigration\OneTimeOperationManager;

abstract class OneTimeOperationProcessJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $operationName,
        public ?Operation $operation = null
    ) {}

    public function handle(): void
    {
        OneTimeOperationManager::getClassObjectByName($this->operationName)->process();

        ray($this->operation);

        $this->operation?->update(['completed_at' => now()]);
    }
}

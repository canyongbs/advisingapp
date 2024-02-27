<?php

namespace AdvisingApp\DataMigration\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use AdvisingApp\DataMigration\OneTimeOperationManager;

abstract class OneTimeOperationProcessJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public string $operationName;

    public function __construct(string $operationName)
    {
        $this->operationName = $operationName;
    }

    public function handle(): void
    {
        OneTimeOperationManager::getClassObjectByName($this->operationName)->process();
    }
}

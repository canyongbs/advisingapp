<?php

namespace AdvisingApp\Campaign\Jobs;

use AdvisingApp\Campaign\Models\CampaignActionEducatable;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\SkipIfBatchCancelled;

abstract class ExecuteCampaignActionOnEducatableJob implements ShouldQueue
{
    use Queueable;
    use Batchable;

    public function __construct(
        public CampaignActionEducatable $actionEducatable,
    ) {}

    /**
     * @return array<object>
     */
    public function middleware(): array
    {
        return [new SkipIfBatchCancelled()];
    }

    abstract public function handle(): void;
}

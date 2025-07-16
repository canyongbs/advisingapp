<?php

namespace AdvisingApp\Workflow\Jobs;

use AdvisingApp\Campaign\Jobs\Middleware\FailIfBatchCancelled;
use AdvisingApp\Workflow\Models\WorkflowRunStep;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

abstract class ExecuteWorkflowActionOnEducatableJob implements ShouldQueue
{
    use Batchable;
    use Queueable;

    public function __construct(public WorkflowRunStep $workflowRunStep) {}

    /**
     * @return array<object>
     */
    public function middleware(): array
    {
        return [new FailIfBatchCancelled()];
    }

    abstract public function handle(): void;
}

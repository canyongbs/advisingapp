<?php

namespace AdvisingApp\Workflow\Jobs;

use AdvisingApp\Workflow\Models\WorkflowRunStep;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExecuteWorkflowAction implements ShouldQueue
{
    use Batchable;
    use Queueable;

    public int $tries = 3;

    public int $timeout = 600;

    public function __construct(public WorkflowRunStep $step) {}

    public function handle(): void
    {
        try {
            $this->batch()->add($this->step->details_type->getActionExecutableJob($this->step));
        } catch (Exception $exception) {
            report($exception);
        }
    }
}

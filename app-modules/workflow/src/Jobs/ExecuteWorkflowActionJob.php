<?php

namespace AdvisingApp\Workflow\Jobs;

use AdvisingApp\Workflow\Models\Contracts\WorkflowAction;
use AdvisingApp\Workflow\Models\WorkflowRunStep;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExecuteWorkflowActionJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public WorkflowAction $action) {}

    public function handle(): void
    {
        $steps = WorkflowRunStep::where('execute_at', '<', now())->whereNull('dispatched_at');

        $steps->each(function (WorkflowRunStep $step) {
            //$step->dispatched_at = now();
            //$step->save();
            //get class for that action
            //execute action; save related model into WorkflowRunStepRelated
            //set succeeded_at or last_failed_at
        });
    }
}

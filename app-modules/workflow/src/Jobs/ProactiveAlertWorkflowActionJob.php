<?php

namespace AdvisingApp\Workflow\Jobs;

use AdvisingApp\Alert\Models\Alert;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\Workflow\Models\WorkflowProactiveAlertDetails;
use AdvisingApp\Workflow\Models\WorkflowRunStepRelated;
use Illuminate\Support\Facades\DB;
use Throwable;

class ProactiveAlertWorkflowActionJob extends ExecuteWorkflowActionOnEducatableJob
{
    public function handle(): void
    {
        try {
            DB::beginTransaction();

            $educatable = $this->workflowRunStep->workflowRun->related;

            assert($educatable instanceof Educatable);

            $details = WorkflowProactiveAlertDetails::whereId($this->workflowRunStep->details_id)->first();

            $alert = Alert::query()->create([
                'concern_type' => $educatable->getMorphClass(),
                'concern_id' => $educatable->getKey(),
                'description' => $details->description,
                'severity' => $details->severity,
                'status_id' => $details->status_id,
                'suggested_intervention' => $details->suggested_intervention,
            ]);

            WorkflowRunStepRelated::create([
                'workflow_run_step_id' => $this->workflowRunStep->id,
                'related' => $alert,
            ]);

            DB::commit();
        } catch (Throwable $throw) {
            DB::rollBack();

            throw $throw;
        }
    }
}

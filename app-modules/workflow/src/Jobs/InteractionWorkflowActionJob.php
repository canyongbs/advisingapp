<?php

namespace AdvisingApp\Workflow\Jobs;

use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\Workflow\Models\WorkflowInteractionDetails;
use AdvisingApp\Workflow\Models\WorkflowRunStepRelated;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

class InteractionWorkflowActionJob extends ExecuteWorkflowActionOnEducatableJob
{
    public function handle(): void
    {
        try {
            DB::beginTransaction();

            $educatable = $this->workflowRunStep->workflowRun->related;

            assert($educatable instanceof Educatable);

            $details = WorkflowInteractionDetails::whereId($this->workflowRunStep->details_id)->first();

            $user = $this->workflowRunStep->workflowRun->workflowTrigger->createdBy;

            assert($user instanceof User);

            $interaction = Interaction::query()->create([
                'user_id' => $user,
                'interactable_type' => $educatable->getMorphClass(),
                'interactable_id' => $educatable->getKey(),
                'interaction_type_id' => $details->interaction_type_id,
                'interaction_initiative_id' => $details->interaction_initiative_id,
                'interaction_relation_id' => $details->interaction_relation_id,
                'interaction_driver_id' => $details->interaction_driver_id,
                'interaction_status_id' => $details->interaction_status_id,
                'interaction_outcome_id' => $details->interaction_outcome_id,
                'division_id' => $details->division_id,
            ]);

            WorkflowRunStepRelated::create([
                'workflow_run_step_id' => $this->workflowRunStep->id,
                'related' => $interaction,
            ]);

            DB::commit();
        } catch (Throwable $throw) {
            DB::rollBack();

            throw $throw;
        }
    }
}

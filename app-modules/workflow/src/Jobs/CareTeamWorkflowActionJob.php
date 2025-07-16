<?php

namespace AdvisingApp\Workflow\Jobs;

use AdvisingApp\CareTeam\Models\CareTeam;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\Workflow\Models\WorkflowCareTeamDetails;
use AdvisingApp\Workflow\Models\WorkflowRunStepRelated;
use Illuminate\Support\Facades\DB;
use Throwable;

class CareTeamWorkflowActionJob extends ExecuteWorkflowActionOnEducatableJob
{
    public function handle(): void
    {
        try {
            DB::beginTransaction();

            $educatable = $this->workflowRunStep->workflowRun->related;

            assert($educatable instanceof Educatable);

            $details = WorkflowCareTeamDetails::whereId($this->workflowRunStep->details_id)->first();

            if($details->remove_prior) {
                $educatable->careTeam()->detach();
            }

            $addedOrUpdatedPivotModels = [];

            foreach($details->care_team as $careTeam) {
                $sync = $educatable
                    ->careTeam()
                    ->syncWithPivotValues(
                        ids: $careTeam['user_id'],
                        values: ['care_team_role_id' => $careTeam['care_team_role_id']],
                        detaching: false,
                    );
                
                $addedOrUpdatedPivotModels[] = $sync['attached'];
                $addedOrUpdatedPivotModels[] = $sync['updated'];
            }

            collect($addedOrUpdatedPivotModels)
                ->flatten()
                ->unique()
                ->each(function (String $addedOrUpdatedPivotModel) use ($educatable) {
                    $careTeam = CareTeam::query()
                        ->where('user_id', $addedOrUpdatedPivotModel)
                        ->where('educatable_type', $educatable->getMorphClass())
                        ->where('educatable_id', $educatable->getKey())
                        ->first();
                    
                    WorkflowRunStepRelated::create([
                        'workflow_run_step_id' => $this->workflowRunStep->id,
                        'related' => $careTeam,
                    ]);
                });

            DB::commit();
        } catch (Throwable $throw) {
            DB::rollBack();

            throw $throw;
        }
    }
}

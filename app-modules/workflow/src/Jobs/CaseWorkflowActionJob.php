<?php

namespace AdvisingApp\Workflow\Jobs;

use AdvisingApp\CaseManagement\Enums\CaseAssignmentStatus;
use AdvisingApp\CaseManagement\Models\CaseModel;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\Workflow\Models\WorkflowCaseDetails;
use AdvisingApp\Workflow\Models\WorkflowRunStepRelated;
use App\Models\User;
use App\Settings\LicenseSettings;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class CaseWorkflowActionJob extends ExecuteWorkflowActionOnEducatableJob
{
    
    public function handle(): void
    {
        try {
            if(! app(LicenseSettings::class)->data->addons->caseManagement) {
                throw new Exception('The Case Management addon is not enabled.');
            }

            DB::beginTransaction();

            $educatable = $this->workflowRunStep->workflowRun->related;

            assert($educatable instanceof Educatable);

            $details = WorkflowCaseDetails::whereId($this->workflowRunStep->details_id)->first();

            $user = $this->workflowRunStep->workflowRun->workflowTrigger->createdBy;

            assert($user instanceof User);

            $case = CaseModel::query()->create([
                'respondent_type' => $educatable->getMorphClass(),
                'respondent_id' => $educatable->getKey(),
                'close_details' => $details->close_details,
                'res_details' => $details->res_details,
                'division_id' => $details->division_id,
                'status_id' => $details->status_id,
                'priority_id' => $details->priority_id,
                'created_by_id' => $user,
            ]);

            if(isset($details->assigned_to_id)) {
                $details->assigned_to_id === 'automatic' ?
                    $case->priority->type->assignment_type->getAssignerClass()->execute($case) :
                    $case->assignments()->create([
                        'user_id' => $details->assigned_to_id,
                        'assigned_by_id' => $user,
                        'assigned_at' => now(),
                        'status' => CaseAssignmentStatus::Active,
                    ]);
            }

            WorkflowRunStepRelated::create([
                'workflow_run_step_id' => $this->workflowRunStep->id,
                'related' => $case,
            ]);

            DB::commit();

        } catch (Throwable $throw) {
            DB::rollBack();

            throw $throw;
        }
    }
}

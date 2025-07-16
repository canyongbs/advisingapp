<?php

namespace AdvisingApp\Workflow\Jobs;

use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\Task\Models\Task;
use AdvisingApp\Workflow\Models\WorkflowRunStepRelated;
use AdvisingApp\Workflow\Models\WorkflowTaskDetails;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

class TaskWorkflowActionJob extends ExecuteWorkflowActionOnEducatableJob
{
    public function handle(): void
    {
        try {
            DB::beginTransaction();

            $educatable = $this->workflowRunStep->workflowRun->related;

            assert($educatable instanceof Educatable);

            $details = WorkflowTaskDetails::whereId($this->workflowRunStep->details_id)->first();

            $user = $this->workflowRunStep->workflowRun->workflowTrigger->createdBy;

            assert($user instanceof User);

            $task = Task::query()->make([
                'title' => $details->title,
                'description' => $details->description,
                'due' => $details->due,
            ]);

            $task->assignedTo()->associate($details->assigned_to);

            $task->createdBy()->associate($user);

            $task->concern()->associate($educatable);

            $task->save();

            WorkflowRunStepRelated::create([
                'workflow_run_step_id' => $this->workflowRunStep->id,
                'related' => $task,
            ]);

            DB::commit();
        } catch (Throwable $throw) {
            DB::rollBack();

            throw $throw;
        }
    }
}

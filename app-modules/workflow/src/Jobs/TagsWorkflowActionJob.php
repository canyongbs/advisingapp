<?php

namespace AdvisingApp\Workflow\Jobs;

use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\Workflow\Models\WorkflowRunStepRelated;
use AdvisingApp\Workflow\Models\WorkflowTagsDetails;
use App\Models\Taggable;
use Illuminate\Support\Facades\DB;
use Throwable;

class TagsWorkflowActionJob extends ExecuteWorkflowActionOnEducatableJob
{
    public function handle(): void
    {
        try{
            DB::beginTransaction();

            $educatable = $this->workflowRunStep->workflowRun->related;

            assert($educatable instanceof Educatable);

            $details = WorkflowTagsDetails::whereId($this->workflowRunStep->details_id)->first();

            $addedOrUpdatedPivotModels = [];

            $sync = $educatable
                ->tags()
                ->sync(
                    ids: $details->tag_ids,
                    detaching: $details->remove_prior,
                );

            $addedOrUpdatedPivotModels[] = $sync['attached'];
            $addedOrUpdatedPivotModels[] = $sync['updated'];

            collect($addedOrUpdatedPivotModels)
                ->flatten()
                ->unique()
                ->each(function (string $addedOrUpdatedPivotModel) use ($educatable) {
                    $taggable = Taggable::query()
                        ->where('tag_id', $addedOrUpdatedPivotModel)
                        ->where('taggable_type', $educatable->getMorphClass())
                        ->where('taggable_id', $educatable->getKey())
                        ->first();

                    WorkflowRunStepRelated::create([
                        'workflow_run_step_id' => $this->workflowRunStep->id,
                        'related' => $taggable,
                    ]);
                });

            DB::commit();
        } catch (Throwable $throw) {
            DB::rollBack();

            throw $throw;
        }
    }
}

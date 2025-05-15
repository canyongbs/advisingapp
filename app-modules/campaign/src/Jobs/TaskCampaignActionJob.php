<?php

namespace AdvisingApp\Campaign\Jobs;

use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\Task\Models\Task;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class TaskCampaignActionJob extends ExecuteCampaignActionOnEducatableJob
{
    public function handle(): void
    {
        try {
            DB::beginTransaction();

            $educatable = $this->actionEducatable->educatable;

            throw_if(
                ! $educatable instanceof Educatable,
                new Exception('The educatable model must implement the Educatable contract.')
            );

            $action = $this->actionEducatable->campaignAction;

            $task = Task::query()->make([
                'title' => $action->data['title'],
                'description' => $action->data['description'],
                'due' => $action->data['due'],
            ]);

            $task->assignedTo()->associate($action->data['assigned_to']);
            $task->createdBy()->associate($action->data['created_by']);
            $task->concern()->associate($educatable);
            $task->save();

            $this->actionEducatable->succeeded_at = now();
            $this->actionEducatable->related()->associate($task);
            $this->actionEducatable->save();

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            $this->actionEducatable->markFailed();

            throw $e;
        }
    }
}

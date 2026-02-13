<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Workflow\Jobs;

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Task\Models\Task;
use AdvisingApp\Task\Notifications\TaskAssignedToUserNotification;
use AdvisingApp\Workflow\Concerns\SchedulesNextWorkflowStep;
use AdvisingApp\Workflow\Models\WorkflowRunStepRelated;
use AdvisingApp\Workflow\Models\WorkflowTaskDetails;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Throwable;

class TaskWorkflowActionJob extends ExecuteWorkflowActionJob
{
    use SchedulesNextWorkflowStep;

    public function handle(): void
    {
        try {
            DB::beginTransaction();

            $educatable = $this->workflowRunStep->workflowRun->related;

            assert($educatable instanceof Educatable);
            assert($educatable instanceof Student || $educatable instanceof Prospect);

            $details = $this->workflowRunStep->details;

            assert($details instanceof WorkflowTaskDetails);

            $user = $this->workflowRunStep->workflowRun->workflowTrigger->createdBy;

            assert($user instanceof User);

            $task = Task::query()->make([
                'title' => $details->title,
                'description' => $details->description,
                'due' => $details->due,
            ]);

            $task->assignedTo()->associate($details->assigned_to);

            if (! empty($task->assignedTo)) {
                $task->assignedTo->notify(new TaskAssignedToUserNotification($task));
            }

            $task->createdBy()->associate($user);

            $task->concern()->associate($educatable);

            $task->save();

            $workflowRunStepRelated = new WorkflowRunStepRelated();

            $workflowRunStepRelated->workflowRunStep()->associate($this->workflowRunStep);
            $workflowRunStepRelated->related()->associate($task);

            $workflowRunStepRelated->save();

            $this->markStepCompletedAndScheduleNext();
            DB::commit();
        } catch (Throwable $throw) {
            DB::rollBack();

            throw $throw;
        }
    }
}

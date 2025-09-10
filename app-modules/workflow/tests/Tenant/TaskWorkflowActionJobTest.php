<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Workflow\Jobs\TaskWorkflowActionJob;
use AdvisingApp\Workflow\Models\WorkflowRun;
use AdvisingApp\Workflow\Models\WorkflowRunStep;
use AdvisingApp\Workflow\Models\WorkflowTaskDetails;
use AdvisingApp\Workflow\Models\WorkflowTrigger;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    Notification::fake();
});

it('executes email workflow step successfully', function () {
    $user = User::factory()->create();
    $student = Student::factory()->create();

    $workflowTrigger = WorkflowTrigger::factory()->create([
        'created_by_type' => User::class,
        'created_by_id' => $user->id,
    ]);

    $workflowRun = WorkflowRun::factory()->create([
        'workflow_trigger_id' => $workflowTrigger->id,
        'related_type' => Student::class,
        'related_id' => $student->getKey(),
    ]);

    $title = fake()->words(3, true);
    $description = fake()->sentence();
    $due = now()->addDay();
    $assignedTo = User::factory()->create();

    $taskDetails = WorkflowTaskDetails::factory()->create([
        'title' => $title,
        'description' => $description,
        'due' => $due,
        'assigned_to' => $assignedTo->id,
    ]);

    $workflowRunStep = WorkflowRunStep::factory()->withDetails($taskDetails)->create([
        'workflow_run_id' => $workflowRun->id,
        'execute_at' => now(),
    ]);

    $job = new TaskWorkflowActionJob($workflowRunStep);
    $job->handle();

    $tasks = $student->tasks()->get();

    expect($tasks)->toHaveCount(1)
        ->and($tasks->first()->title)->toEqual($title)
        ->and($tasks->first()->description)->toEqual($description)
        ->and($tasks->first()->due->toString())->toEqual($due->toString())
        ->and($tasks->first()->assignedTo->is($assignedTo))->toBeTrue();

    $workflowRunStep->refresh();
    expect($workflowRunStep->succeeded_at)->not->toBeNull();
});

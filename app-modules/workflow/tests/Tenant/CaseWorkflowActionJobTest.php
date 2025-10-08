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

use AdvisingApp\CaseManagement\Models\CasePriority;
use AdvisingApp\CaseManagement\Models\CaseStatus;
use AdvisingApp\Division\Models\Division;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Team\Models\Team;
use AdvisingApp\Workflow\Jobs\CaseWorkflowActionJob;
use AdvisingApp\Workflow\Models\WorkflowCaseDetails;
use AdvisingApp\Workflow\Models\WorkflowRun;
use AdvisingApp\Workflow\Models\WorkflowRunStep;
use AdvisingApp\Workflow\Models\WorkflowRunStepRelated;
use AdvisingApp\Workflow\Models\WorkflowTrigger;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

it('executes case workflow step successfully', function () {
    Notification::fake();

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

    $division = Division::factory()->create();
    $caseStatus = CaseStatus::factory()->create();
    $casePriority = CasePriority::factory()->create();
    $assignedTo = User::factory()->for(Team::factory()->hasAttached($casePriority->type, relationship: 'manageableCaseTypes'))->create();
    $closeDetails = fake()->sentence();
    $resDetails = fake()->sentence();

    $caseDetails = WorkflowCaseDetails::factory()->create([
        'division_id' => $division,
        'status_id' => $caseStatus,
        'priority_id' => $casePriority,
        'assigned_to_id' => $assignedTo,
        'close_details' => $closeDetails,
        'res_details' => $resDetails,
    ]);

    $workflowRunStep = WorkflowRunStep::factory()->withDetails($caseDetails)->create([
        'workflow_run_id' => $workflowRun->id,
        'execute_at' => now(),
    ]);

    $job = new CaseWorkflowActionJob($workflowRunStep);
    $job->handle();

    $cases = $student->cases()->get(); 

    expect($cases)->toHaveCount(1)
        ->and($cases->first()->id)->toBe(WorkflowRunStepRelated::where('workflow_run_step_id', $workflowRunStep->id)->first()->related->getKey())
        ->and($cases->first()->division->getKey())->toEqual($division->getKey())
        ->and($cases->first()->status->getKey())->toEqual($caseStatus->getKey())
        ->and($cases->first()->priority->getKey())->toEqual($casePriority->getKey())
        ->and($cases->first()->assignments()->first()->user_id)->toEqual($assignedTo->getKey())
        ->and($cases->first()->close_details)->toEqual($closeDetails)
        ->and($cases->first()->res_details)->toEqual($resDetails);

    $workflowRunStep->refresh();
    expect($workflowRunStep->succeeded_at)->not->toBeNull();
});

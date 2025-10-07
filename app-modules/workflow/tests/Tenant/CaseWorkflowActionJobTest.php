<?php

use AdvisingApp\CaseManagement\Models\CasePriority;
use AdvisingApp\CaseManagement\Models\CaseStatus;
use AdvisingApp\Division\Models\Division;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Team\Models\Team;
use AdvisingApp\Workflow\Jobs\CaseWorkflowActionJob;
use AdvisingApp\Workflow\Models\WorkflowCaseDetails;
use AdvisingApp\Workflow\Models\WorkflowRun;
use AdvisingApp\Workflow\Models\WorkflowRunStep;
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
        ->and($cases->first()->division->getKey())->toEqual($division->getKey())
        ->and($cases->first()->status->getKey())->toEqual($caseStatus->getKey())
        ->and($cases->first()->priority->getKey())->toEqual($casePriority->getKey())
        ->and($cases->first()->assignments()->first()->user_id)->toEqual($assignedTo->getKey())
        ->and($cases->first()->close_details)->toEqual($closeDetails)
        ->and($cases->first()->res_details)->toEqual($resDetails);

    $workflowRunStep->refresh();
    expect($workflowRunStep->succeeded_at)->not->toBeNull();
});
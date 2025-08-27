<?php

use AdvisingApp\Application\Database\Seeders\ApplicationSubmissionStateSeeder;
use AdvisingApp\Application\Events\ApplicationSubmissionCreated;
use AdvisingApp\Application\Listeners\TriggerApplicationSubmissionWorkflows;
use AdvisingApp\Application\Models\Application;
use AdvisingApp\Application\Models\ApplicationSubmission;
use AdvisingApp\Workflow\Enums\WorkflowTriggerType;
use AdvisingApp\Workflow\Models\Workflow;
use AdvisingApp\Workflow\Models\WorkflowRun;
use AdvisingApp\Workflow\Models\WorkflowTrigger;
use App\Models\User;
use Illuminate\Support\Facades\Event;

use function Pest\Laravel\seed;

beforeEach(function () {
    seed(ApplicationSubmissionStateSeeder::class);
});

it('triggers workflows when application submission is created', function () {
    $application = Application::factory()->create();
    $user = User::factory()->create();

    $workflowTrigger = WorkflowTrigger::factory()->create([
        'type' => WorkflowTriggerType::EventBased,
        'related_type' => $application->getMorphClass(),
        'related_id' => $application->id,
        'created_by_id' => $user->id,
    ]);

    $workflow = Workflow::factory()->create([
        'workflow_trigger_id' => $workflowTrigger->id,
        'is_enabled' => true,
    ]);

    $submission = ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
    ]);

    expect(WorkflowRun::count())->toBe(1);

    $workflowRun = WorkflowRun::first();
    expect($workflowRun->workflow_trigger_id)->toBe($workflowTrigger->id);
    expect($workflowRun->workflowTrigger->workflow->id)->toBe($workflow->id);
    expect($workflowRun->related_type)->toBe($submission->author->getMorphClass());
    expect($workflowRun->related_id)->toBe($submission->author->getKey());
});

it('does not trigger disabled workflows', function () {
    $application = Application::factory()->create();
    $user = User::factory()->create();

    $workflowTrigger = WorkflowTrigger::factory()->create([
        'type' => WorkflowTriggerType::EventBased,
        'related_type' => $application->getMorphClass(),
        'related_id' => $application->id,
        'created_by_id' => $user->id,
    ]);

    $workflow = Workflow::factory()->create([
        'workflow_trigger_id' => $workflowTrigger->id,
        'is_enabled' => false,
    ]);

    $submission = ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
    ]);

    expect(WorkflowRun::count())->toBe(0);

    event(new ApplicationSubmissionCreated($submission));

    expect(WorkflowRun::count())->toBe(0);
});

test('ApplicationSubmissionCreated event has TriggerApplicationSubmissionWorkflows listener', function () {
    Event::fake();

    Event::assertListening(
        expectedEvent: ApplicationSubmissionCreated::class,
        expectedListener: TriggerApplicationSubmissionWorkflows::class
    );
});

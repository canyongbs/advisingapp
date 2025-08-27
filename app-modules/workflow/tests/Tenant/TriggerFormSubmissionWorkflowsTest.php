<?php

use AdvisingApp\Form\Events\FormSubmissionCreated;
use AdvisingApp\Form\Listeners\TriggerFormSubmissionWorkflows;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\FormSubmission;
use AdvisingApp\Workflow\Enums\WorkflowTriggerType;
use AdvisingApp\Workflow\Models\Workflow;
use AdvisingApp\Workflow\Models\WorkflowRun;
use AdvisingApp\Workflow\Models\WorkflowTrigger;
use App\Models\User;
use Illuminate\Support\Facades\Event;

it('triggers workflows when form submission is created', function () {
    $form = Form::factory()->create();
    $user = User::factory()->create();

    $workflowTrigger = WorkflowTrigger::factory()->create([
        'type' => WorkflowTriggerType::EventBased,
        'related_type' => $form->getMorphClass(),
        'related_id' => $form->id,
        'created_by_id' => $user->id,
    ]);

    $workflow = Workflow::factory()->create([
        'workflow_trigger_id' => $workflowTrigger->id,
        'is_enabled' => true,
    ]);

    expect(WorkflowRun::count())->toBe(0);

    $submission = FormSubmission::factory()->create([
        'form_id' => $form->id,
    ]);

    expect(WorkflowRun::count())->toBe(1);

    $workflowRun = WorkflowRun::first();
    expect($workflowRun->workflow_trigger_id)->toBe($workflowTrigger->id);
    expect($workflowRun->workflowTrigger->workflow->id)->toBe($workflow->id);
    expect($workflowRun->related_type)->toBe($submission->author->getMorphClass());
    expect($workflowRun->related_id)->toBe($submission->author->getKey());
});

it('does not trigger workflows for submissions without authors', function () {
    $form = Form::factory()->create();
    $user = User::factory()->create();

    $workflowTrigger = WorkflowTrigger::factory()->create([
        'type' => WorkflowTriggerType::EventBased,
        'related_type' => $form->getMorphClass(),
        'related_id' => $form->id,
        'created_by_id' => $user->id,
    ]);

    $workflow = Workflow::factory()->create([
        'workflow_trigger_id' => $workflowTrigger->id,
        'is_enabled' => true,
    ]);

    $submission = FormSubmission::factory()->create([
        'form_id' => $form->id,
        'author_id' => null,
        'author_type' => null,
    ]);

    expect(WorkflowRun::count())->toBe(0);

    event(new FormSubmissionCreated($submission));

    expect(WorkflowRun::count())->toBe(0);
});

it('does not trigger disabled workflows', function () {
    $form = Form::factory()->create();
    $user = User::factory()->create();

    $workflowTrigger = WorkflowTrigger::factory()->create([
        'type' => WorkflowTriggerType::EventBased,
        'related_type' => $form->getMorphClass(),
        'related_id' => $form->id,
        'created_by_id' => $user->id,
    ]);

    $workflow = Workflow::factory()->create([
        'workflow_trigger_id' => $workflowTrigger->id,
        'is_enabled' => false,
    ]);

    $submission = FormSubmission::factory()->create([
        'form_id' => $form->id,
    ]);

    expect(WorkflowRun::count())->toBe(0);

    event(new FormSubmissionCreated($submission));

    expect(WorkflowRun::count())->toBe(0);
});

test('FormSubmissionCreated event has TriggerFormSubmissionWorkflows listener', function () {
    Event::fake();

    Event::assertListening(
        expectedEvent: FormSubmissionCreated::class,
        expectedListener: TriggerFormSubmissionWorkflows::class
    );
});

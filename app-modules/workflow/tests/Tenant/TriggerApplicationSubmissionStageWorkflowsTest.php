<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\Application\Enums\ApplicationSubmissionStateClassification;
use AdvisingApp\Application\Events\ApplicationSubmissionStateEntered;
use AdvisingApp\Application\Events\ApplicationSubmissionStateExited;
use AdvisingApp\Application\Listeners\TriggerApplicationSubmissionStageWorkflows;
use AdvisingApp\Application\Models\Application;
use AdvisingApp\Application\Models\ApplicationSubmission;
use AdvisingApp\Application\Models\ApplicationSubmissionState;
use AdvisingApp\Workflow\Enums\WorkflowTriggerEvent;
use AdvisingApp\Workflow\Enums\WorkflowTriggerType;
use AdvisingApp\Workflow\Models\Workflow;
use AdvisingApp\Workflow\Models\WorkflowRun;
use AdvisingApp\Workflow\Models\WorkflowTrigger;
use App\Models\User;
use Illuminate\Support\Facades\Event;

it('triggers Enter workflows when application submission is created', function () {
    $receivedState = ApplicationSubmissionState::factory()
        ->state(['classification' => ApplicationSubmissionStateClassification::Received])
        ->create();

    $application = Application::factory()->create();

    $workflowTrigger = WorkflowTrigger::factory()
        ->for($application, 'related')
        ->for(User::factory(), 'createdBy')
        ->for($receivedState, 'subRelated')
        ->state([
            'type' => WorkflowTriggerType::EventBased,
            'event' => WorkflowTriggerEvent::Enter,
        ])
        ->create();

    $workflow = Workflow::factory()
        ->for($workflowTrigger)
        ->state(['is_enabled' => true])
        ->create();

    $submission = ApplicationSubmission::factory()
        ->for($application, 'submissible')
        ->create();

    expect(WorkflowRun::count())->toBe(1);

    $workflowRun = WorkflowRun::first();
    expect($workflowRun->workflow_trigger_id)->toBe($workflowTrigger->id);
    expect($workflowRun->workflowTrigger->workflow->id)->toBe($workflow->id);
    expect($workflowRun->related_type)->toBe($submission->author->getMorphClass());
    expect($workflowRun->related_id)->toBe($submission->author->getKey());
});

it('triggers Exit workflows when submission state changes', function () {
    $receivedState = ApplicationSubmissionState::factory()
        ->state(['classification' => ApplicationSubmissionStateClassification::Received])
        ->create();

    $reviewState = ApplicationSubmissionState::factory()
        ->state(['classification' => ApplicationSubmissionStateClassification::Review])
        ->create();

    $application = Application::factory()->create();

    $exitTrigger = WorkflowTrigger::factory()
        ->for($application, 'related')
        ->for(User::factory(), 'createdBy')
        ->for($receivedState, 'subRelated')
        ->state([
            'type' => WorkflowTriggerType::EventBased,
            'event' => WorkflowTriggerEvent::Exit,
        ])
        ->create();

    Workflow::factory()
        ->for($exitTrigger)
        ->state(['is_enabled' => true])
        ->create();

    $submission = ApplicationSubmission::factory()
        ->for($application, 'submissible')
        ->create();

    // Enter on Received fired during creation; we only care about the Exit run on transition.
    WorkflowRun::query()->delete();

    $submission->state()->associate($reviewState);
    $submission->save();

    expect(WorkflowRun::count())->toBe(1);
    expect(WorkflowRun::first()->workflow_trigger_id)->toBe($exitTrigger->id);
});

it('triggers both Exit and Enter workflows on a single state transition', function () {
    $receivedState = ApplicationSubmissionState::factory()
        ->state(['classification' => ApplicationSubmissionStateClassification::Received])
        ->create();

    $reviewState = ApplicationSubmissionState::factory()
        ->state(['classification' => ApplicationSubmissionStateClassification::Review])
        ->create();

    $application = Application::factory()->create();
    $user = User::factory()->create();

    $exitReceivedTrigger = WorkflowTrigger::factory()
        ->for($application, 'related')
        ->for($user, 'createdBy')
        ->for($receivedState, 'subRelated')
        ->state([
            'type' => WorkflowTriggerType::EventBased,
            'event' => WorkflowTriggerEvent::Exit,
        ])
        ->has(Workflow::factory()->state(['is_enabled' => true]))
        ->create();

    $enterReviewTrigger = WorkflowTrigger::factory()
        ->for($application, 'related')
        ->for($user, 'createdBy')
        ->for($reviewState, 'subRelated')
        ->state([
            'type' => WorkflowTriggerType::EventBased,
            'event' => WorkflowTriggerEvent::Enter,
        ])
        ->has(Workflow::factory()->state(['is_enabled' => true]))
        ->create();

    $submission = ApplicationSubmission::factory()
        ->for($application, 'submissible')
        ->create();

    // Discard the Enter-on-Received run that fired during creation.
    WorkflowRun::query()->delete();

    $submission->state()->associate($reviewState);
    $submission->save();

    $triggerIds = WorkflowRun::query()->pluck('workflow_trigger_id')->all();

    expect($triggerIds)->toHaveCount(2);
    expect($triggerIds)->toContain($exitReceivedTrigger->id, $enterReviewTrigger->id);
});

it('does not trigger workflows whose stage does not match the transition', function () {
    // Received must exist so the observer can pick it as the submission's default state.
    ApplicationSubmissionState::factory()
        ->state(['classification' => ApplicationSubmissionStateClassification::Received])
        ->create();

    $reviewState = ApplicationSubmissionState::factory()
        ->state(['classification' => ApplicationSubmissionStateClassification::Review])
        ->create();

    $application = Application::factory()->create();

    WorkflowTrigger::factory()
        ->for($application, 'related')
        ->for(User::factory(), 'createdBy')
        ->for($reviewState, 'subRelated')
        ->state([
            'type' => WorkflowTriggerType::EventBased,
            'event' => WorkflowTriggerEvent::Enter,
        ])
        ->has(Workflow::factory()->state(['is_enabled' => true]))
        ->create();

    ApplicationSubmission::factory()
        ->for($application, 'submissible')
        ->create();

    expect(WorkflowRun::count())->toBe(0);
});

it('does not trigger disabled workflows', function () {
    $receivedState = ApplicationSubmissionState::factory()
        ->state(['classification' => ApplicationSubmissionStateClassification::Received])
        ->create();

    $application = Application::factory()->create();

    WorkflowTrigger::factory()
        ->for($application, 'related')
        ->for(User::factory(), 'createdBy')
        ->for($receivedState, 'subRelated')
        ->state([
            'type' => WorkflowTriggerType::EventBased,
            'event' => WorkflowTriggerEvent::Enter,
        ])
        ->has(Workflow::factory()->state(['is_enabled' => false]))
        ->create();

    ApplicationSubmission::factory()
        ->for($application, 'submissible')
        ->create();

    expect(WorkflowRun::count())->toBe(0);
});

test('ApplicationSubmissionStateEntered event is wired to the stage workflows listener', function () {
    Event::fake();

    Event::assertListening(
        expectedEvent: ApplicationSubmissionStateEntered::class,
        expectedListener: [TriggerApplicationSubmissionStageWorkflows::class, 'handleEntered']
    );
});

test('ApplicationSubmissionStateExited event is wired to the stage workflows listener', function () {
    Event::fake();

    Event::assertListening(
        expectedEvent: ApplicationSubmissionStateExited::class,
        expectedListener: [TriggerApplicationSubmissionStageWorkflows::class, 'handleExited']
    );
});

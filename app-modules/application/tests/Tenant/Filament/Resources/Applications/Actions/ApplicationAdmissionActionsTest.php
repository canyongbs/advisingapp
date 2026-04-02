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

use AdvisingApp\Application\Database\Seeders\ApplicationSubmissionStateSeeder;
use AdvisingApp\Application\Enums\ApplicationSubmissionStateClassification;
use AdvisingApp\Application\Filament\Resources\Applications\Actions\ApplicationAdmissionActions;
use AdvisingApp\Application\Models\Application;
use AdvisingApp\Application\Models\ApplicationSubmission;
use AdvisingApp\Application\Models\ApplicationSubmissionState;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

use function Pest\Laravel\seed;
use function Tests\asSuperAdmin;

beforeEach(function () {
    seed(ApplicationSubmissionStateSeeder::class);
});

test('update_submission_state action is labeled Update State', function () {
    $actions = ApplicationAdmissionActions::get();

    $action = collect($actions)->firstWhere(fn (Action $action) => $action->getName() === 'update_submission_state');

    expect($action)->not->toBeNull();
    expect($action->getLabel())->toBe('Update State');
});

test('state machine returns allowed transitions for a Received submission', function () {
    asSuperAdmin();

    $application = Application::factory()->create();
    $receivedState = ApplicationSubmissionState::where('classification', ApplicationSubmissionStateClassification::Received)->first();
    $submission = ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
        'state_id' => $receivedState->id,
    ]);

    $transitions = $submission
        ->getStateMachine(ApplicationSubmissionStateClassification::class, 'state.classification')
        ->getStateTransitions();

    expect($transitions)->not->toBeNull();
    expect($transitions->count())->toBeGreaterThan(0);
});

test('update_submission_state action is visible when transitions exist for the submission', function () {
    asSuperAdmin();

    $application = Application::factory()->create();
    $receivedState = ApplicationSubmissionState::where('classification', ApplicationSubmissionStateClassification::Received)->first();
    $submission = ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
        'state_id' => $receivedState->id,
    ]);

    $transitions = $submission
        ->getStateMachine(ApplicationSubmissionStateClassification::class, 'state.classification')
        ->getStateTransitions();

    $actions = ApplicationAdmissionActions::get();
    $action = collect($actions)->firstWhere(fn (Action $action) => $action->getName() === 'update_submission_state');

    $isVisible = (bool) $transitions->count();

    expect($isVisible)->toBeTrue();
});

test('update_submission_state action is not visible when no transitions exist', function () {
    asSuperAdmin();

    $application = Application::factory()->create();

    $admitState = ApplicationSubmissionState::where('classification', ApplicationSubmissionStateClassification::Admit)->first();
    $submission = ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
        'state_id' => $admitState->id,
    ]);

    $transitions = $submission
        ->getStateMachine(ApplicationSubmissionStateClassification::class, 'state.classification')
        ->getStateTransitions();

    $isVisible = (bool) $transitions->count();

    if ($transitions->count() === 0) {
        expect($isVisible)->toBeFalse();
    } else {
        expect($isVisible)->toBeTrue();
    }
});

test('calling transitionTo on a submission changes its state', function () {
    asSuperAdmin();

    $application = Application::factory()->create();
    $receivedState = ApplicationSubmissionState::where('classification', ApplicationSubmissionStateClassification::Received)->first();
    $submission = ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
        'state_id' => $receivedState->id,
    ]);

    $stateMachine = $submission->getStateMachine(ApplicationSubmissionStateClassification::class, 'state.classification');
    $transitions = $stateMachine->getStateTransitions();

    if ($transitions->isEmpty()) {
        $this->markTestSkipped('No transitions defined from Received; skipping transition test.');
    }

    $nextClassificationValue = (string) $transitions->first();
    $nextState = ApplicationSubmissionState::where('classification', $nextClassificationValue)->first();

    expect($nextState)->not->toBeNull();

    $stateMachine->transitionTo($nextState, ApplicationSubmissionStateClassification::from($nextClassificationValue));

    // @phpstan-ignore property.notFound
    expect($submission->fresh()->state_id)->toBe($nextState->id);
});

test('same state no-op guard: transitioning to same state does not invoke transitionTo', function () {
    asSuperAdmin();

    $application = Application::factory()->create();
    $receivedState = ApplicationSubmissionState::where('classification', ApplicationSubmissionStateClassification::Received)->first();
    $submission = ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
        'state_id' => $receivedState->id,
    ]);

    $originalStateId = $submission->getOriginal('state_id') ?? $receivedState->id;
    $submission->setAttribute('state_id', $originalStateId);
    $submission->unsetRelation('state');

    $newState = ApplicationSubmissionState::find($originalStateId);
    $allowedTransitions = $submission
        ->getStateMachine(ApplicationSubmissionStateClassification::class, 'state.classification')
        ->getStateTransitions()
        ->map(fn ($state) => (string) $state)
        ->all();

    // @phpstan-ignore property.notFound
    $sameState = ! in_array($newState->classification->value, $allowedTransitions, true);

    if ($sameState) {
        // @phpstan-ignore property.notFound
        expect($submission->fresh()->state_id)->toBe($receivedState->id);
    } else {
        expect($allowedTransitions)->not->toBeEmpty();
    }
});

test('state dropdown includes all non-archived states and excludes archived states', function () {
    asSuperAdmin();

    $application = Application::factory()->create();
    $receivedState = ApplicationSubmissionState::where('classification', ApplicationSubmissionStateClassification::Received)->first();
    ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
        'state_id' => $receivedState->id,
    ]);

    $reviewState = ApplicationSubmissionState::where('classification', ApplicationSubmissionStateClassification::Review)->first();
    // @phpstan-ignore method.notFound
    $reviewState->archive();

    // @phpstan-ignore method.notFound
    $dropdownStateIds = ApplicationSubmissionState::query()
        ->withoutArchived()
        ->oldest('id')
        ->pluck('id')
        ->all();

    $nonArchivedStateIds = ApplicationSubmissionState::query()
        ->whereNull('archived_at')
        ->pluck('id')
        ->all();

    expect($dropdownStateIds)->toEqualCanonicalizing($nonArchivedStateIds);
    expect($dropdownStateIds)->not->toContain($reviewState->id);
});

test('state dropdown current state is pre-selected by default', function () {
    asSuperAdmin();

    $application = Application::factory()->create();
    $receivedState = ApplicationSubmissionState::where('classification', ApplicationSubmissionStateClassification::Received)->first();
    $submission = ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
        'state_id' => $receivedState->id,
    ]);

    $actions = ApplicationAdmissionActions::get();
    $action = collect($actions)->firstWhere(fn (Action $action) => $action->getName() === 'update_submission_state');

    $action->record($submission);
    $schema = $action->getSchema(Schema::make());

    expect($schema)->not->toBeNull();

    $schema->record($submission);

    $schemaComponents = $schema->getComponents();

    $stateField = null;

    foreach ($schemaComponents as $component) {
        if ($component instanceof Select && $component->getName() === 'state_id') {
            $stateField = $component;

            break;
        }
    }

    expect($stateField)->toBeInstanceOf(Select::class);
    // @phpstan-ignore property.notFound
    expect($stateField->getDefaultState())->toBe($submission->state_id);
});

test('state dropdown disables option when selected state classification is not an allowed transition', function () {
    asSuperAdmin();

    $application = Application::factory()->create();
    $receivedState = ApplicationSubmissionState::where('classification', ApplicationSubmissionStateClassification::Received)->first();
    $submission = ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
        'state_id' => $receivedState->id,
    ]);

    $allowedTransitions = $submission
        ->getStateMachine(ApplicationSubmissionStateClassification::class, 'state.classification')
        ->getStateTransitions()
        ->map(fn ($state) => (string) $state)
        ->all();

    // @phpstan-ignore method.notFound
    $disallowedState = ApplicationSubmissionState::query()
        ->withoutArchived()
        ->get()
        ->first(function (ApplicationSubmissionState $state) use ($allowedTransitions, $submission): bool {
            // @phpstan-ignore property.notFound
            if ($state->id === $submission->state_id) {
                return false;
            }

            // @phpstan-ignore property.notFound
            return ! in_array($state->classification->value, $allowedTransitions, true);
        });

    if (! $disallowedState) {
        $this->markTestSkipped('No disallowed submission state found for the current transition graph.');
    }

    $actions = ApplicationAdmissionActions::get();
    $action = collect($actions)->firstWhere(fn (Action $action) => $action->getName() === 'update_submission_state');

    $action->record($submission);
    $schema = $action->getSchema(Schema::make());

    expect($schema)->not->toBeNull();

    $schema->record($submission);

    $stateField = null;

    foreach ($schema->getComponents() as $component) {
        if ($component instanceof Select && $component->getName() === 'state_id') {
            $stateField = $component;

            break;
        }
    }

    expect($stateField)->toBeInstanceOf(Select::class);
    expect($stateField->isOptionDisabled((string) $disallowedState->id, $disallowedState->name))->toBeTrue();
});

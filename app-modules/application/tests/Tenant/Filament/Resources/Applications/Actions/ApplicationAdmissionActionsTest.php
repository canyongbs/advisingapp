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
use AdvisingApp\Application\Filament\Resources\Applications\Actions\ApplicationAdmissionActions;
use AdvisingApp\Application\Filament\Resources\Applications\Pages\ManageApplicationSubmissions;
use AdvisingApp\Application\Models\Application;
use AdvisingApp\Application\Models\ApplicationSubmission;
use AdvisingApp\Application\Models\ApplicationSubmissionState;
use Filament\Actions\Action;
use Filament\Actions\Testing\TestAction;
use Filament\Forms\Components\Select;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('update_submission_state action is labeled Update State', function () {
    $actions = ApplicationAdmissionActions::get();

    $action = collect($actions)->firstWhere(fn (Action $action) => $action->getName() === 'update_submission_state');

    expect($action)->not->toBeNull();
    expect($action->getLabel())->toBe('Update State');
});

test('update_submission_state action is visible when submission state has allowed transitions', function () {
    asSuperAdmin();

    $receivedState = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
    ]);

    $application = Application::factory()->create();

    $submission = ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
        'state_id' => $receivedState->id,
    ]);

    livewire(ManageApplicationSubmissions::class, ['record' => $application->getKey()])
        ->mountAction(TestAction::make('view')->table($submission))
        ->assertActionVisible('update_submission_state');
});

test('update_submission_state action is not visible when submission state has no allowed transitions', function () {
    asSuperAdmin();

    $admitState = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Admit,
    ]);

    $application = Application::factory()->create();

    $submission = ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
        'state_id' => $admitState->id,
    ]);

    livewire(ManageApplicationSubmissions::class, ['record' => $application->getKey()])
        ->mountAction(TestAction::make('view')->table($submission))
        ->assertActionHidden('update_submission_state');
});

test('calling the update_submission_state action transitions the submission to the selected state', function () {
    asSuperAdmin();

    $receivedState = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
    ]);

    $reviewState = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Review,
    ]);

    $application = Application::factory()->create();

    $submission = ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
        'state_id' => $receivedState->id,
    ]);

    livewire(ManageApplicationSubmissions::class, ['record' => $application->getKey()])
        ->mountAction(TestAction::make('view')->table($submission))
        ->callAction('update_submission_state', data: ['state_id' => $reviewState->id]);

    expect($submission->fresh()->state_id)->toBe($reviewState->id);
});

test('state dropdown excludes archived states that are not the currently selected state', function () {
    asSuperAdmin();

    $receivedState = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
    ]);

    $reviewState = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Review,
    ]);

    $application = Application::factory()->create();

    $submission = ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
        'state_id' => $receivedState->id,
    ]);

    // @phpstan-ignore method.notFound
    $reviewState->archive();

    livewire(ManageApplicationSubmissions::class, ['record' => $application->getKey()])
        ->mountAction(TestAction::make('view')->table($submission))
        ->mountAction('update_submission_state')
        ->assertFormFieldExists('state_id', checkFieldUsing: function (Select $field) use ($receivedState, $reviewState) {
            $optionKeys = array_keys($field->getOptions());

            expect($optionKeys)->toContain($receivedState->id)
                ->and($optionKeys)->not->toContain($reviewState->id);

            return true;
        });
});

test('state dropdown pre-selects the current submission state by default', function () {
    asSuperAdmin();

    $receivedState = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
    ]);

    $application = Application::factory()->create();

    $submission = ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
        'state_id' => $receivedState->id,
    ]);

    livewire(ManageApplicationSubmissions::class, ['record' => $application->getKey()])
        ->mountAction(TestAction::make('view')->table($submission))
        ->mountAction('update_submission_state')
        ->assertSchemaStateSet(['state_id' => $submission->state_id]);
});

test('calling the update_submission_state action with a disallowed transition state does not change the submission state', function () {
    asSuperAdmin();

    $receivedState = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
    ]);

    // Admit is not a valid transition target from Received (only Review is allowed)
    $admitState = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Admit,
    ]);

    $application = Application::factory()->create();

    $submission = ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
        'state_id' => $receivedState->id,
    ]);

    livewire(ManageApplicationSubmissions::class, ['record' => $application->getKey()])
        ->mountAction(TestAction::make('view')->table($submission))
        ->callAction('update_submission_state', data: ['state_id' => $admitState->id]);

    // The state machine rejects the disallowed transition, so the state must remain unchanged
    expect($submission->fresh()->state_id)->toBe($receivedState->id);
});

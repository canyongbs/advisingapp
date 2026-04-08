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

use AdvisingApp\Application\Enums\ApplicationSubmissionStateClassification;
use AdvisingApp\Application\Filament\Resources\Applications\Pages\ManageApplicationSubmissions;
use AdvisingApp\Application\Models\Application;
use AdvisingApp\Application\Models\ApplicationSubmission;
use AdvisingApp\Application\Models\ApplicationSubmissionState;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('tabs are generated for each state', function () {
    asSuperAdmin();

    $receivedState = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
    ]);

    $reviewState = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Review,
    ]);

    $application = Application::factory()->create();

    $tabs = livewire(ManageApplicationSubmissions::class, ['record' => $application->getKey()])
        ->instance()
        ->getTabs();

    expect($tabs)->toHaveKey('all');
    expect($tabs)->toHaveKey($receivedState->id);
    expect($tabs)->toHaveKey($reviewState->id);
});

test('tab label includes Archived when the state for that classification is archived but still has submissions', function () {
    asSuperAdmin();

    $receivedState = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
    ]);

    $application = Application::factory()->create();

    ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
        'state_id' => $receivedState->id,
    ]);

    // @phpstan-ignore method.notFound
    $receivedState->archive();

    $tabs = livewire(ManageApplicationSubmissions::class, ['record' => $application->getKey()])
        ->instance()
        ->getTabs();

    $receivedKey = $receivedState->id;

    expect($tabs)->toHaveKey($receivedKey);
    expect($tabs[$receivedKey]->getLabel())->toContain('(Archived)');
});

test('default active tab is the first non-archived state', function () {
    asSuperAdmin();

    $receivedState = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
    ]);

    $application = Application::factory()->create();

    $defaultTab = livewire(ManageApplicationSubmissions::class, ['record' => $application->getKey()])
        ->instance()
        ->getDefaultActiveTab();

    expect($defaultTab)->toBe($receivedState->id);
});

test('default tab falls back to first non-archived state when first created state is archived and unused', function () {
    asSuperAdmin();

    $receivedState = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
    ]);

    $reviewState = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Review,
    ]);

    // @phpstan-ignore method.notFound
    $receivedState->archive();

    $application = Application::factory()->create();

    $defaultTab = livewire(ManageApplicationSubmissions::class, ['record' => $application->getKey()])
        ->instance()
        ->getDefaultActiveTab();

    expect($defaultTab)->toBe($reviewState->id);
});

test('archived state that has submissions still appears as a tab', function () {
    asSuperAdmin();

    $receivedState = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
    ]);

    $application = Application::factory()->create();
    ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
        'state_id' => $receivedState->id,
    ]);

    // @phpstan-ignore method.notFound
    $receivedState->archive();

    $tabs = livewire(ManageApplicationSubmissions::class, ['record' => $application->getKey()])
        ->instance()
        ->getTabs();

    expect($tabs)->toHaveKey($receivedState->id);
});

test('multiple states with the same classification each get their own tab', function () {
    asSuperAdmin();

    $firstReceivedState = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
    ]);

    $secondReceivedState = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
    ]);

    $application = Application::factory()->create();

    $tabs = livewire(ManageApplicationSubmissions::class, ['record' => $application->getKey()])
        ->instance()
        ->getTabs();

    expect($tabs)->toHaveKey($firstReceivedState->id);
    expect($tabs)->toHaveKey($secondReceivedState->id);
});

test('switching tabs filters the table to only show submissions for that state', function () {
    asSuperAdmin();

    $receivedState = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
    ]);

    $reviewState = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Review,
    ]);

    $application = Application::factory()->create();

    // Delete auto-created submissions from the Application factory
    $application->submissions()->forceDelete();

    $receivedSubmission = ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
    ]);
    // Observer auto-assigns received state; explicitly set review for the second
    $reviewSubmission = ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
    ]);
    $reviewSubmission->state()->associate($reviewState);
    $reviewSubmission->saveQuietly();

    livewire(ManageApplicationSubmissions::class, ['record' => $application->getKey()])
        ->set('activeTab', $receivedState->id)
        ->assertCanSeeTableRecords([$receivedSubmission])
        ->assertCanNotSeeTableRecords([$reviewSubmission])
        ->set('activeTab', $reviewState->id)
        ->assertCanSeeTableRecords([$reviewSubmission])
        ->assertCanNotSeeTableRecords([$receivedSubmission]);
});

test('all tab shows submissions from all states', function () {
    asSuperAdmin();

    $receivedState = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
    ]);

    $reviewState = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Review,
    ]);

    $application = Application::factory()->create();

    // Delete auto-created submissions from the Application factory
    $application->submissions()->forceDelete();

    $receivedSubmission = ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
    ]);

    $reviewSubmission = ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
    ]);
    $reviewSubmission->state()->associate($reviewState);
    $reviewSubmission->saveQuietly();

    livewire(ManageApplicationSubmissions::class, ['record' => $application->getKey()])
        ->set('activeTab', 'all')
        ->assertCanSeeTableRecords([$receivedSubmission, $reviewSubmission]);
});

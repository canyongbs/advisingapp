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
use AdvisingApp\Application\Filament\Resources\Applications\Pages\ManageApplicationSubmissions;
use AdvisingApp\Application\Models\Application;
use AdvisingApp\Application\Models\ApplicationSubmission;
use AdvisingApp\Application\Models\ApplicationSubmissionState;

use function Pest\Laravel\seed;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

beforeEach(function () {
    seed(ApplicationSubmissionStateSeeder::class);
});

test('tabs are generated for each unique classification', function () {
    asSuperAdmin();

    $application = Application::factory()->create();

    $tabs = livewire(ManageApplicationSubmissions::class, ['record' => $application->getKey()])
        ->instance()
        ->getTabs();

    expect($tabs)->toHaveKey('all');

    foreach (ApplicationSubmissionStateClassification::cases() as $classification) {
        $stateExists = ApplicationSubmissionState::where('classification', $classification)->exists();

        if ($stateExists) {
            expect($tabs)->toHaveKey(strtolower($classification->value));
        }
    }
});

test('all tab is always present in tabs', function () {
    asSuperAdmin();

    $application = Application::factory()->create();

    $tabs = livewire(ManageApplicationSubmissions::class, ['record' => $application->getKey()])
        ->instance()
        ->getTabs();

    expect($tabs)->toHaveKey('all');
});

test('tab label includes Archived when the state for that classification is archived but still has submissions', function () {
    asSuperAdmin();

    $application = Application::factory()->create();

    $receivedState = ApplicationSubmissionState::where('classification', ApplicationSubmissionStateClassification::Received)
        ->first();

    ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
        'state_id' => $receivedState->id,
    ]);

    // @phpstan-ignore method.notFound
    $receivedState->archive();

    $tabs = livewire(ManageApplicationSubmissions::class, ['record' => $application->getKey()])
        ->instance()
        ->getTabs();

    $receivedKey = strtolower(ApplicationSubmissionStateClassification::Received->value);

    expect($tabs)->toHaveKey($receivedKey);
    expect($tabs[$receivedKey]->getLabel())->toContain('(Archived)');
});

test('tab label does not include Archived when no state in classification is archived', function () {
    asSuperAdmin();

    $application = Application::factory()->create();

    $tabs = livewire(ManageApplicationSubmissions::class, ['record' => $application->getKey()])
        ->instance()
        ->getTabs();

    $receivedKey = strtolower(ApplicationSubmissionStateClassification::Received->value);

    expect($tabs[$receivedKey]->getLabel())->not->toContain('(Archived)');
});

test('default active tab is received classification when seeded Received state is not archived', function () {
    asSuperAdmin();

    $application = Application::factory()->create();

    $defaultTab = livewire(ManageApplicationSubmissions::class, ['record' => $application->getKey()])
        ->instance()
        ->getDefaultActiveTab();

    expect($defaultTab)->toBe(strtolower(ApplicationSubmissionStateClassification::Received->value));
});

test('default tab falls back to first non-received classification when Received is archived and unused', function () {
    asSuperAdmin();

    $receivedState = ApplicationSubmissionState::where('classification', ApplicationSubmissionStateClassification::Received)
        ->first();

    // @phpstan-ignore method.notFound
    $receivedState->archive();

    $application = Application::factory()->create();

    $defaultTab = livewire(ManageApplicationSubmissions::class, ['record' => $application->getKey()])
        ->instance()
        ->getDefaultActiveTab();

    expect($defaultTab)->not->toBe(strtolower(ApplicationSubmissionStateClassification::Received->value));
    expect($defaultTab)->not->toBe('all');
});

test('archived state with no submissions does not appear as a tab key', function () {
    asSuperAdmin();

    $receivedState = ApplicationSubmissionState::where('classification', ApplicationSubmissionStateClassification::Received)
        ->first();

    // @phpstan-ignore method.notFound
    $receivedState->archive();

    $application = Application::factory()->create();

    $tabs = livewire(ManageApplicationSubmissions::class, ['record' => $application->getKey()])
        ->instance()
        ->getTabs();

    expect($tabs)->not->toHaveKey(strtolower(ApplicationSubmissionStateClassification::Received->value));
});

test('archived state that has submissions still appears as a tab', function () {
    asSuperAdmin();

    $receivedState = ApplicationSubmissionState::where('classification', ApplicationSubmissionStateClassification::Received)
        ->first();

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

    expect($tabs)->toHaveKey(strtolower(ApplicationSubmissionStateClassification::Received->value));
});

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
use AdvisingApp\Application\Filament\Resources\Applications\ApplicationResource;
use AdvisingApp\Application\Filament\Resources\Applications\Pages\ManageApplication;
use AdvisingApp\Application\Filament\Resources\Applications\RelationManagers\SubmissionsRelationManager;
use AdvisingApp\Application\Models\Application;
use AdvisingApp\Application\Models\ApplicationSubmission;
use AdvisingApp\Application\Models\ApplicationSubmissionState;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Models\User;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\followingRedirects;
use function Tests\asSuperAdmin;

$testSubmissions = fn (Application $application): Testable => Livewire::test(SubmissionsRelationManager::class, [
    'ownerRecord' => $application,
    'pageClass' => ManageApplication::class,
]);

test('tabs are generated for each state', function () use ($testSubmissions) {
    asSuperAdmin();

    $receivedState = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
    ]);

    $reviewState = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Review,
    ]);

    $application = Application::factory()->create();

    $tabs = $testSubmissions($application)
        ->instance()
        ->getTabs();

    expect($tabs)->toHaveKey('all');
    expect($tabs)->toHaveKey($receivedState->id);
    expect($tabs)->toHaveKey($reviewState->id);
});

test('tab label includes Archived when the state for that classification is archived but still has submissions', function () use ($testSubmissions) {
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

    $tabs = $testSubmissions($application)
        ->instance()
        ->getTabs();

    $receivedKey = $receivedState->id;

    expect($tabs)->toHaveKey($receivedKey);
    expect($tabs[$receivedKey]->getLabel())->toContain('(Archived)');
});

test('default active tab is the state with is_default true', function () use ($testSubmissions) {
    asSuperAdmin();

    ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
        'is_default' => false,
    ]);

    $defaultState = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Review,
        'is_default' => true,
    ]);

    $application = Application::factory()->create();

    $defaultTab = $testSubmissions($application)
        ->instance()
        ->getDefaultActiveTab();

    expect($defaultTab)->toBe($defaultState->id);
});

test('default active tab falls back to all when the default state is archived and unused', function () use ($testSubmissions) {
    asSuperAdmin();

    $defaultState = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
        'is_default' => true,
    ]);

    $application = Application::factory()->create();

    // Ensure the default state has no submissions so it is treated as unused
    $application->submissions()->forceDelete();

    // @phpstan-ignore method.notFound
    $defaultState->archive();

    $defaultTab = $testSubmissions($application)
        ->instance()
        ->getDefaultActiveTab();

    expect($defaultTab)->toBe('all');
});

test('default tab falls back to all when no state has is_default true', function () use ($testSubmissions) {
    asSuperAdmin();

    ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
        'is_default' => false,
    ]);

    ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Review,
        'is_default' => false,
    ]);

    $application = Application::factory()->create();

    $defaultTab = $testSubmissions($application)
        ->instance()
        ->getDefaultActiveTab();

    expect($defaultTab)->toBe('all');
});

test('archived state that has submissions still appears as a tab', function () use ($testSubmissions) {
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

    $tabs = $testSubmissions($application)
        ->instance()
        ->getTabs();

    expect($tabs)->toHaveKey($receivedState->id);
});

test('multiple states with the same classification each get their own tab', function () use ($testSubmissions) {
    asSuperAdmin();

    $firstReceivedState = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
    ]);

    $secondReceivedState = ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
    ]);

    $application = Application::factory()->create();

    $tabs = $testSubmissions($application)
        ->instance()
        ->getTabs();

    expect($tabs)->toHaveKey($firstReceivedState->id);
    expect($tabs)->toHaveKey($secondReceivedState->id);
});

test('switching tabs filters the table to only show submissions for that state', function () use ($testSubmissions) {
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

    $testSubmissions($application)
        ->set('activeTab', $receivedState->id)
        ->assertCanSeeTableRecords([$receivedSubmission])
        ->assertCanNotSeeTableRecords([$reviewSubmission])
        ->set('activeTab', $reviewState->id)
        ->assertCanSeeTableRecords([$reviewSubmission])
        ->assertCanNotSeeTableRecords([$receivedSubmission]);
});

test('all tab shows submissions from all states', function () use ($testSubmissions) {
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

    $testSubmissions($application)
        ->set('activeTab', 'all')
        ->assertCanSeeTableRecords([$receivedSubmission, $reviewSubmission]);
});

test('archive action is visible when submission is not archived', function () use ($testSubmissions) {
    asSuperAdmin();

    ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
    ]);

    $application = Application::factory()->create();
    $submission = ApplicationSubmission::factory()->create(['application_id' => $application->id]);

    $testSubmissions($application)
        ->assertTableActionVisible('archive', $submission);
});

test('archive action successfully archives a submission', function () use ($testSubmissions) {
    asSuperAdmin();

    ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
    ]);

    $application = Application::factory()->create();
    $submission = ApplicationSubmission::factory()->create(['application_id' => $application->id]);

    expect($submission->isArchived())->toBeFalse();

    $testSubmissions($application)
        ->callTableAction('archive', $submission)
        ->assertNotified();

    expect($submission->fresh()->isArchived())->toBeTrue();
});

test('bulk archive action successfully archives multiple submissions', function () use ($testSubmissions) {
    asSuperAdmin();

    ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
    ]);

    $application = Application::factory()->create();
    $application->submissions()->forceDelete();

    $submissions = ApplicationSubmission::factory()->count(3)->create(['application_id' => $application->id]);

    $submissions->each(function (ApplicationSubmission $submission): void {
        expect($submission->isArchived())->toBeFalse();
    });

    $testSubmissions($application)
        ->callTableBulkAction('archive', $submissions)
        ->assertNotified();

    $submissions->each(function (ApplicationSubmission $submission): void {
        expect($submission->fresh()->isArchived())->toBeTrue();
    });
});

test('archived submissions are hidden by default', function () use ($testSubmissions) {
    asSuperAdmin();

    ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
    ]);

    $application = Application::factory()->create();
    $application->submissions()->forceDelete();

    $activeSubmission = ApplicationSubmission::factory()->create(['application_id' => $application->id]);
    $archivedSubmission = ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
        'archived_at' => now(),
    ]);

    $testSubmissions($application)
        ->assertCanSeeTableRecords([$activeSubmission])
        ->assertCanNotSeeTableRecords([$archivedSubmission]);
});

test('archived submissions are visible when the withoutArchived filter is removed', function () use ($testSubmissions) {
    asSuperAdmin();

    ApplicationSubmissionState::factory()->create([
        'classification' => ApplicationSubmissionStateClassification::Received,
    ]);

    $application = Application::factory()->create();
    $application->submissions()->forceDelete();

    $activeSubmission = ApplicationSubmission::factory()->create(['application_id' => $application->id]);
    $archivedSubmission = ApplicationSubmission::factory()->create([
        'application_id' => $application->id,
        'archived_at' => now(),
    ]);

    $testSubmissions($application)
        ->removeTableFilter('withoutArchived')
        ->assertCanSeeTableRecords([$activeSubmission, $archivedSubmission]);
});

describe('submission links', function () {
    it('opens the linked submission when the page loads', function (string $page) {
        asSuperAdmin();

        ApplicationSubmissionState::factory()->create(['classification' => ApplicationSubmissionStateClassification::Received]);
        $application = Application::factory()->create();
        $submission = $application->submissions()->firstOrFail();
        $query = ['tableAction' => 'view', 'tableActionRecord' => $submission->getKey()];

        $response = followingRedirects()
            ->get(ApplicationResource::getUrl($page, ['record' => $application, 'tab' => 'submissions', ...$query]))
            ->assertSuccessful();

        $document = new DOMDocument();
        $document->loadHTML($response->getContent(), LIBXML_NOERROR | LIBXML_NOWARNING);
        $initializers = (new DOMXPath($document))->query('//*[@class="fi-resource-relation-manager"]/following-sibling::*[@*[name()="wire:init"]]');
        assert($initializers !== false);
        $initializer = $initializers->item(0);
        assert($initializer instanceof DOMElement);

        expect($initializers->length)->toBe(1)
            ->and($initializer->getAttribute('wire:init'))->toStartWith('mountAction(');

        $component = Livewire::withQueryParams($query)
            ->test(SubmissionsRelationManager::class, [
                'ownerRecord' => $application,
                'pageClass' => ManageApplication::class,
            ]);
        $manager = $component->instance();

        $component
            ->call('mountAction', $manager->defaultTableAction, [], $manager->getDefaultTableActionUrlContext())
            ->assertSet('mountedActions.0.name', 'view')
            ->assertSet('mountedActions.0.context.recordKey', $submission->getKey());
    })->with(['view', 'manage-submissions']);
});

describe('authorization', function () use ($testSubmissions) {
    it('denies access to the submissions without the `application.view-any` permission', function () use ($testSubmissions) {
        actingAs(User::factory()->licensed(LicenseType::cases())->create());

        ApplicationSubmissionState::factory()->create(['classification' => ApplicationSubmissionStateClassification::Received]);
        $application = Application::factory()->create();

        $testSubmissions($application)->assertForbidden();
    });

    it('does not open a submission from another application through a link', function () {
        asSuperAdmin();

        ApplicationSubmissionState::factory()->create(['classification' => ApplicationSubmissionStateClassification::Received]);
        $application = Application::factory()->create();
        $foreignSubmission = Application::factory()->create()->submissions()->firstOrFail();

        $component = Livewire::withQueryParams(['tableAction' => 'view', 'tableActionRecord' => $foreignSubmission->getKey()])
            ->test(SubmissionsRelationManager::class, [
                'ownerRecord' => $application,
                'pageClass' => ManageApplication::class,
            ]);
        $manager = $component->instance();

        $component
            ->call('mountAction', $manager->defaultTableAction, [], $manager->getDefaultTableActionUrlContext())
            ->assertSet('mountedActions', []);
    });

    it('does not archive submissions without the `application.*.update` permission', function (bool $isBulk) use ($testSubmissions) {
        $user = User::factory()->licensed(LicenseType::cases())->create();
        $user->givePermissionTo('application.view-any', 'application.*.view');
        actingAs($user);

        ApplicationSubmissionState::factory()->create(['classification' => ApplicationSubmissionStateClassification::Received]);
        $application = Application::factory()->create();
        $submission = $application->submissions()->firstOrFail();

        expect($submission->isArchived())->toBeFalse();

        $component = $testSubmissions($application)->set('activeTab', 'all');

        if ($isBulk) {
            $component->selectTableRecords([$submission->getKey()]);
        }

        $component
            ->call('mountAction', 'archive', [], $isBulk ? ['table' => true, 'bulk' => true] : ['table' => true, 'recordKey' => $submission->getKey()])
            ->call('callMountedAction');

        expect($submission->fresh()->isArchived())->toBeFalse();
    })->with([
        'single' => false,
        'bulk' => true,
    ]);
});

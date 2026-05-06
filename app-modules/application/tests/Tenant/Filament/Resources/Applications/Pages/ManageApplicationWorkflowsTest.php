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

use AdvisingApp\Application\Database\Seeders\ApplicationSubmissionStateSeeder;
use AdvisingApp\Application\Enums\ApplicationSubmissionStateClassification;
use AdvisingApp\Application\Filament\Resources\Applications\Pages\ManageApplicationWorkflows;
use AdvisingApp\Application\Models\Application;
use AdvisingApp\Application\Models\ApplicationSubmissionState;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Workflow\Enums\WorkflowTriggerEvent;
use AdvisingApp\Workflow\Enums\WorkflowTriggerType;
use AdvisingApp\Workflow\Filament\Resources\Workflows\Pages\EditWorkflow;
use AdvisingApp\Workflow\Models\Workflow;
use AdvisingApp\Workflow\Models\WorkflowTrigger;
use App\Models\User;
use Filament\Actions\DeleteAction;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\seed;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

beforeEach(function () {
    seed(ApplicationSubmissionStateSeeder::class);
});

test('can successfully create a new workflow for an application through manage workflows page', function () {
    asSuperAdmin();

    $application = Application::factory()->create();
    $user = User::first();
    expect(WorkflowTrigger::count())->toBe(0);

    livewire(ManageApplicationWorkflows::class, ['record' => $application->getKey()])
        ->callAction('create');

    expect(Workflow::count())->toBe(1);
    expect(WorkflowTrigger::count())->toBe(1);

    $workflow = Workflow::first();
    $workflowTrigger = WorkflowTrigger::first();

    expect($workflow->name)->toBe('Application Workflow');
    expect($workflow->is_enabled)->toBeFalse();
    expect($workflow->workflow_trigger_id)->toBe($workflowTrigger->id);

    expect($workflowTrigger->type)->toBe(WorkflowTriggerType::EventBased);
    expect($workflowTrigger->related_type)->toBe($application->getMorphClass());
    expect($workflowTrigger->related_id)->toBe($application->id);
    expect($workflowTrigger->created_by_id)->toBe($user->id);
    expect($workflowTrigger->created_by_type)->toBe($user->getMorphClass());
});

test('creates multiple workflows for the same application without conflicts', function () {
    asSuperAdmin();

    $application = Application::factory()->create();

    expect(Workflow::count())->toBe(0);

    livewire(ManageApplicationWorkflows::class, ['record' => $application->getKey()])
        ->callAction('create');

    expect(Workflow::count())->toBe(1);

    livewire(ManageApplicationWorkflows::class, ['record' => $application->getKey()])
        ->callAction('create');

    expect(Workflow::count())->toBe(2);

    $workflows = Workflow::all();
    $workflowTriggers = WorkflowTrigger::all();

    foreach ($workflowTriggers as $trigger) {
        expect($trigger->related_type)->toBe($application->getMorphClass());
        expect($trigger->related_id)->toBe($application->id);
    }

    foreach ($workflows as $workflow) {
        expect($workflow->name)->toBe('Application Workflow');
        expect($workflow->is_enabled)->toBeFalse();
    }
});

test('application workflow creation is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();
    $application = Application::factory()->create();

    actingAs($user);

    livewire(ManageApplicationWorkflows::class, ['record' => $application->getKey()])
        ->assertForbidden();

    expect(Workflow::count())->toBe(0);
    expect(WorkflowTrigger::count())->toBe(0);

    $user->givePermissionTo('application.view-any');
    $user->givePermissionTo('application.*.update');

    livewire(ManageApplicationWorkflows::class, ['record' => $application->getKey()])
        ->callAction('create');

    expect(Workflow::count())->toBe(1);
    expect(WorkflowTrigger::count())->toBe(1);

    $workflowTrigger = WorkflowTrigger::first();
    expect($workflowTrigger->created_by_id)->toBe($user->id);
    expect($workflowTrigger->related_id)->toBe($application->id);
});

test('can successfully edit workflow name through edit workflow page', function () {
    asSuperAdmin();

    $application = Application::factory()->create();
    $oldWorkflow = Workflow::factory()
        ->for(
            WorkflowTrigger::factory()
                ->state([
                    'related_type' => $application->getMorphClass(),
                    'related_id' => $application->id,
                    'sub_related_type' => 'application_submission_state',
                    'sub_related_id' => ApplicationSubmissionState::query()
                        ->where('classification', ApplicationSubmissionStateClassification::Received)
                        ->value('id'),
                    'event' => WorkflowTriggerEvent::Enter,
                ])
        )
        ->create();

    $faker = fake();
    $newWorkflowName = $faker->sentence(3);

    livewire(EditWorkflow::class, ['record' => $oldWorkflow->getKey()])
        ->fillForm(['name' => $newWorkflowName])
        ->call('save')
        ->assertHasNoFormErrors();

    $oldWorkflow->refresh();
    expect($oldWorkflow->name)->toBe($newWorkflowName);
});

test('can enable workflow through edit workflow page', function () {
    asSuperAdmin();

    $application = Application::factory()->create();
    $workflow = Workflow::factory()
        ->for(
            WorkflowTrigger::factory()
                ->state([
                    'related_type' => $application->getMorphClass(),
                    'related_id' => $application->id,
                    'sub_related_type' => 'application_submission_state',
                    'sub_related_id' => ApplicationSubmissionState::query()
                        ->where('classification', ApplicationSubmissionStateClassification::Received)
                        ->value('id'),
                    'event' => WorkflowTriggerEvent::Enter,
                ])
        )
        ->state(['is_enabled' => false])
        ->create();

    livewire(EditWorkflow::class, ['record' => $workflow->getKey()])
        ->fillForm(['is_enabled' => true])
        ->call('save')
        ->assertHasNoFormErrors();

    $workflow->refresh();
    expect($workflow->is_enabled)->toBeTrue();
});

test('can disable workflow through edit workflow page', function () {
    asSuperAdmin();

    $application = Application::factory()->create();
    $workflow = Workflow::factory()
        ->for(
            WorkflowTrigger::factory()
                ->state([
                    'related_type' => $application->getMorphClass(),
                    'related_id' => $application->id,
                    'sub_related_type' => 'application_submission_state',
                    'sub_related_id' => ApplicationSubmissionState::query()
                        ->where('classification', ApplicationSubmissionStateClassification::Received)
                        ->value('id'),
                    'event' => WorkflowTriggerEvent::Enter,
                ])
        )
        ->state(['is_enabled' => true])
        ->create();

    livewire(EditWorkflow::class, ['record' => $workflow->getKey()])
        ->fillForm(['is_enabled' => false])
        ->call('save')
        ->assertHasNoFormErrors();

    $workflow->refresh();
    expect($workflow->is_enabled)->toBeFalse();
});

test('validates workflow name is required when editing', function () {
    asSuperAdmin();

    $application = Application::factory()->create();
    $workflow = Workflow::factory()
        ->for(
            WorkflowTrigger::factory()
                ->state([
                    'related_type' => $application->getMorphClass(),
                    'related_id' => $application->id,
                    'sub_related_type' => 'application_submission_state',
                    'sub_related_id' => ApplicationSubmissionState::query()
                        ->where('classification', ApplicationSubmissionStateClassification::Received)
                        ->value('id'),
                    'event' => WorkflowTriggerEvent::Enter,
                ])
        )
        ->create();

    livewire(EditWorkflow::class, ['record' => $workflow->getKey()])
        ->fillForm(['name' => ''])
        ->call('save')
        ->assertHasFormErrors(['name' => 'required']);
});

test('validates workflow name has maximum length when editing', function () {
    asSuperAdmin();

    $application = Application::factory()->create();
    $workflow = Workflow::factory()
        ->for(
            WorkflowTrigger::factory()
                ->state([
                    'related_type' => $application->getMorphClass(),
                    'related_id' => $application->id,
                    'sub_related_type' => 'application_submission_state',
                    'sub_related_id' => ApplicationSubmissionState::query()
                        ->where('classification', ApplicationSubmissionStateClassification::Received)
                        ->value('id'),
                    'event' => WorkflowTriggerEvent::Enter,
                ])
        )
        ->create();

    $longName = str_repeat('a', 256);

    livewire(EditWorkflow::class, ['record' => $workflow->getKey()])
        ->fillForm(['name' => $longName])
        ->call('save')
        ->assertHasFormErrors(['name']);
});

test('workflow editing succeeds with proper permissions', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();
    $application = Application::factory()->create();
    $oldWorkflow = Workflow::factory()
        ->for(
            WorkflowTrigger::factory()
                ->state([
                    'related_type' => $application->getMorphClass(),
                    'related_id' => $application->id,
                    'sub_related_type' => 'application_submission_state',
                    'sub_related_id' => ApplicationSubmissionState::query()
                        ->where('classification', ApplicationSubmissionStateClassification::Received)
                        ->value('id'),
                    'event' => WorkflowTriggerEvent::Enter,
                ])
        )
        ->create();

    $user->givePermissionTo('application.view-any');
    $user->givePermissionTo('application.*.update');

    $faker = fake();
    $newWorkflowName = $faker->sentence(3);

    actingAs($user);

    livewire(EditWorkflow::class, ['record' => $oldWorkflow->getKey()])
        ->fillForm(['name' => $newWorkflowName])
        ->call('save')
        ->assertHasNoFormErrors();

    $oldWorkflow->refresh();
    expect($oldWorkflow->name)->toBe($newWorkflowName);
});

test('workflow deletion succeeds with proper permissions', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();
    $application = Application::factory()->create();
    $workflow = Workflow::factory()
        ->for(
            WorkflowTrigger::factory()
                ->state([
                    'related_type' => $application->getMorphClass(),
                    'related_id' => $application->id,
                    'sub_related_type' => 'application_submission_state',
                    'sub_related_id' => ApplicationSubmissionState::query()
                        ->where('classification', ApplicationSubmissionStateClassification::Received)
                        ->value('id'),
                    'event' => WorkflowTriggerEvent::Enter,
                ])
        )
        ->create();

    $user->givePermissionTo('application.view-any');
    $user->givePermissionTo('application.*.update');
    $user->givePermissionTo('application.*.delete');

    $triggerId = $workflow->workflow_trigger_id;

    actingAs($user);

    livewire(EditWorkflow::class, [
        'record' => $workflow->getRouteKey(),
    ])
        ->assertActionExists(DeleteAction::class)
        ->assertActionEnabled(DeleteAction::class)
        ->callAction(DeleteAction::class);

    assertSoftDeleted($workflow);

    expect(WorkflowTrigger::find($triggerId))->not->toBeNull();
});

test('create action persists Stage and Trigger event from form data', function () {
    asSuperAdmin();

    $application = Application::factory()->create();

    $reviewState = ApplicationSubmissionState::query()
        ->where('classification', ApplicationSubmissionStateClassification::Review)
        ->firstOrFail();

    livewire(ManageApplicationWorkflows::class, ['record' => $application->getKey()])
        ->callAction('create', [
            'sub_related_id' => $reviewState->id,
            'event' => WorkflowTriggerEvent::Exit->value,
        ]);

    $workflowTrigger = WorkflowTrigger::firstOrFail();
    expect($workflowTrigger->sub_related_type)->toBe($reviewState->getMorphClass());
    expect($workflowTrigger->sub_related_id)->toBe($reviewState->id);
    expect($workflowTrigger->event)->toBe(WorkflowTriggerEvent::Exit);
});

test('create action defaults Stage to first non-archived state when no tab is active', function () {
    asSuperAdmin();

    $application = Application::factory()->create();

    $firstState = ApplicationSubmissionState::query()
        // @phpstan-ignore method.notFound
        ->withoutArchived()
        ->oldest('id')
        ->firstOrFail();

    livewire(ManageApplicationWorkflows::class, ['record' => $application->getKey()])
        ->callAction('create');

    $workflowTrigger = WorkflowTrigger::firstOrFail();
    expect($workflowTrigger->sub_related_type)->toBe($firstState->getMorphClass());
    expect($workflowTrigger->sub_related_id)->toBe($firstState->id);
    expect($workflowTrigger->event)->toBe(WorkflowTriggerEvent::Enter);
});

test('tabs render one per non-archived submission state plus All', function () {
    asSuperAdmin();

    $application = Application::factory()->create();

    $expectedStateIds = ApplicationSubmissionState::query()
        // @phpstan-ignore method.notFound
        ->withoutArchived()
        ->oldest('id')
        ->pluck('id')
        ->all();

    $tabs = livewire(ManageApplicationWorkflows::class, ['record' => $application->getKey()])
        ->instance()
        ->getTabs();

    foreach ($expectedStateIds as $stateId) {
        expect($tabs)->toHaveKey($stateId);
    }

    expect($tabs)->toHaveKey('all');
});

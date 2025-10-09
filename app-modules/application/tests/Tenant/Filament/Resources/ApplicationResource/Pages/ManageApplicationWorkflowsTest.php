<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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
use AdvisingApp\Application\Filament\Resources\ApplicationResource\Pages\ManageApplicationWorkflows;
use AdvisingApp\Application\Models\Application;
use AdvisingApp\Workflow\Enums\WorkflowTriggerType;
use AdvisingApp\Workflow\Models\Workflow;
use AdvisingApp\Workflow\Models\WorkflowTrigger;
use App\Models\User;

use function Pest\Laravel\assertDatabaseHas;
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

    assertDatabaseHas(Workflow::class, [
        'name' => 'Application Workflow',
        'is_enabled' => false,
        'workflow_trigger_id' => $workflowTrigger->id,
    ]);

    assertDatabaseHas(WorkflowTrigger::class, [
        'type' => WorkflowTriggerType::EventBased->value,
        'related_type' => $application->getMorphClass(),
        'related_id' => $application->id,
        'created_by_id' => $user->id,
    ]);
});

test('creates workflow with proper database transaction behavior', function () {
    asSuperAdmin();

    $application = Application::factory()->create();

    expect(Workflow::count())->toBe(0);
    expect(WorkflowTrigger::count())->toBe(0);

    livewire(ManageApplicationWorkflows::class, ['record' => $application->getKey()])
        ->callAction('create');

    expect(Workflow::count())->toBe(1);
    expect(WorkflowTrigger::count())->toBe(1);
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

test('requires proper authentication to create workflows', function () {
    $application = Application::factory()->create();

    livewire(ManageApplicationWorkflows::class, ['record' => $application->getKey()])
        ->assertForbidden();

    expect(Workflow::count())->toBe(0);
    expect(WorkflowTrigger::count())->toBe(0);
});

test('authenticated super admin user can create workflows', function () {
    asSuperAdmin();

    $application = Application::factory()->create();

    livewire(ManageApplicationWorkflows::class, ['record' => $application->getKey()])
        ->callAction('create');

    expect(Workflow::count())->toBe(1);
    expect(WorkflowTrigger::count())->toBe(1);

    $workflowTrigger = WorkflowTrigger::first();
    $user = User::first();
    expect($workflowTrigger->created_by_id)->toBe($user->id);
});

test('creates workflow trigger with correct application relationships', function () {
    asSuperAdmin();

    $application = Application::factory()->create();

    livewire(ManageApplicationWorkflows::class, ['record' => $application->getKey()])
        ->callAction('create');

    $workflowTrigger = WorkflowTrigger::first();

    expect($workflowTrigger->related)->toBeInstanceOf(Application::class);
    $relatedApplication = $workflowTrigger->related;
    assert($relatedApplication instanceof Application);
    expect($relatedApplication->id)->toBe($application->id);
    expect($relatedApplication->name)->toBe($application->name);
});

test('creates workflow for application with different configurations', function (callable $applicationFactory, array $expectedConfig): void {
    asSuperAdmin();

    $application = $applicationFactory();

    livewire(ManageApplicationWorkflows::class, ['record' => $application->getKey()])
        ->callAction('create');

    expect(Workflow::count())->toBe(1);
    expect(WorkflowTrigger::count())->toBe(1);

    $workflowTrigger = WorkflowTrigger::first();
    expect($workflowTrigger->related_id)->toBe($application->id);

    foreach ($expectedConfig as $attribute => $value) {
        expect($application->{$attribute})->toBe($value);
    }
})->with([
    'application with description' => [
        fn () => Application::factory()->state(['description' => 'Test application description'])->create(),
        ['description' => 'Test application description'],
    ],
]);

test('handles workflow creation gracefully when application does not exist', function () {
    asSuperAdmin();

    $application = Application::factory()->create();
    $applicationId = $application->id;
    $application->delete();

    expect(function () use ($applicationId) {
        $nonExistentApplication = new Application(['id' => $applicationId]);
        livewire(ManageApplicationWorkflows::class, ['record' => $nonExistentApplication->getKey()])
            ->callAction('create');
    })->toThrow(Exception::class);

    expect(Workflow::count())->toBe(0);
    expect(WorkflowTrigger::count())->toBe(0);
});

test('creates workflow in disabled state by default for safety', function () {
    asSuperAdmin();

    $application = Application::factory()->create();

    livewire(ManageApplicationWorkflows::class, ['record' => $application->getKey()])
        ->callAction('create');

    $workflow = Workflow::first();

    expect($workflow->is_enabled)->toBeFalse();

    assertDatabaseHas(Workflow::class, [
        'is_enabled' => false,
    ]);
});

test('creates workflow with event-based trigger type for application submissions', function () {
    asSuperAdmin();

    $application = Application::factory()->create();

    livewire(ManageApplicationWorkflows::class, ['record' => $application->getKey()])
        ->callAction('create');

    $workflowTrigger = WorkflowTrigger::first();

    expect($workflowTrigger->type)->toBe(WorkflowTriggerType::EventBased);

    assertDatabaseHas(WorkflowTrigger::class, [
        'type' => WorkflowTriggerType::EventBased->value,
    ]);
});

test('workflow creation integrates properly with existing application workflows list', function () {
    asSuperAdmin();

    $application = Application::factory()->create();

    Workflow::factory()
        ->count(2)
        ->for(
            WorkflowTrigger::factory()
                ->state([
                    'related_type' => $application->getMorphClass(),
                    'related_id' => $application->id,
                ])
        )
        ->create();

    expect(Workflow::count())->toBe(2);

    livewire(ManageApplicationWorkflows::class, ['record' => $application->getKey()])
        ->callAction('create');

    expect(Workflow::count())->toBe(3);

    $allTriggers = WorkflowTrigger::all();

    foreach ($allTriggers as $trigger) {
        expect($trigger->related_id)->toBe($application->id);
    }
});

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

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Form\Filament\Resources\FormResource\Pages\ManageFormWorkflows;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Workflow\Enums\WorkflowTriggerType;
use AdvisingApp\Workflow\Filament\Resources\Workflows\Pages\EditWorkflow;
use AdvisingApp\Workflow\Models\Workflow;
use AdvisingApp\Workflow\Models\WorkflowTrigger;
use App\Models\User;
use Filament\Actions\DeleteAction;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('can successfully create a new workflow for a form through manage workflows page', function () {
    asSuperAdmin();

    $form = Form::factory()->create();
    $user = User::first();
    expect(WorkflowTrigger::count())->toBe(0);

    livewire(ManageFormWorkflows::class, ['record' => $form->getKey()])
        ->callAction('create');

    expect(Workflow::count())->toBe(1);
    expect(WorkflowTrigger::count())->toBe(1);

    $workflow = Workflow::first();
    $workflowTrigger = WorkflowTrigger::first();

    expect($workflow->name)->toBe('Form Workflow');
    expect($workflow->is_enabled)->toBeFalse();
    expect($workflow->workflow_trigger_id)->toBe($workflowTrigger->id);

    expect($workflowTrigger->type)->toBe(WorkflowTriggerType::EventBased);
    expect($workflowTrigger->related_type)->toBe($form->getMorphClass());
    expect($workflowTrigger->related_id)->toBe($form->id);
    expect($workflowTrigger->created_by_id)->toBe($user->id);
    expect($workflowTrigger->created_by_type)->toBe($user->getMorphClass());
});

test('creates multiple workflows for the same form without conflicts', function () {
    asSuperAdmin();

    $form = Form::factory()->create();

    expect(Workflow::count())->toBe(0);

    livewire(ManageFormWorkflows::class, ['record' => $form->getKey()])
        ->callAction('create');

    expect(Workflow::count())->toBe(1);

    livewire(ManageFormWorkflows::class, ['record' => $form->getKey()])
        ->callAction('create');

    expect(Workflow::count())->toBe(2);

    $workflows = Workflow::all();
    $workflowTriggers = WorkflowTrigger::all();

    foreach ($workflowTriggers as $trigger) {
        expect($trigger->related_type)->toBe($form->getMorphClass());
        expect($trigger->related_id)->toBe($form->id);
    }

    foreach ($workflows as $workflow) {
        expect($workflow->name)->toBe('Form Workflow');
        expect($workflow->is_enabled)->toBeFalse();
    }
});

test('form workflow creation is gated with proper access control', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();
    $form = Form::factory()->create();

    actingAs($user);

    livewire(ManageFormWorkflows::class, ['record' => $form->getKey()])
        ->assertForbidden();

    expect(Workflow::count())->toBe(0);
    expect(WorkflowTrigger::count())->toBe(0);

    $user->givePermissionTo('form.view-any');
    $user->givePermissionTo('form.*.update');

    livewire(ManageFormWorkflows::class, ['record' => $form->getKey()])
        ->callAction('create');

    expect(Workflow::count())->toBe(1);
    expect(WorkflowTrigger::count())->toBe(1);

    $workflowTrigger = WorkflowTrigger::first();
    expect($workflowTrigger->created_by_id)->toBe($user->id);
    expect($workflowTrigger->related_id)->toBe($form->id);
});

test('creates workflow in disabled state by default for safety', function () {
    asSuperAdmin();

    $form = Form::factory()->create();

    livewire(ManageFormWorkflows::class, ['record' => $form->getKey()])
        ->callAction('create');

    $workflow = Workflow::first();

    expect($workflow->is_enabled)->toBeFalse();
});

test('form workflow editing succeeds with proper permissions', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();
    $form = Form::factory()->create();
    $oldWorkflow = Workflow::factory()
        ->for(
            WorkflowTrigger::factory()
                ->state([
                    'related_type' => $form->getMorphClass(),
                    'related_id' => $form->id,
                ])
        )
        ->create();

    $user->givePermissionTo('form.view-any');
    $user->givePermissionTo('form.*.update');

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

test('can successfully edit workflow name for a form workflow', function () {
    asSuperAdmin();

    $form = Form::factory()->create();
    $oldWorkflow = Workflow::factory()
        ->for(
            WorkflowTrigger::factory()
                ->state([
                    'related_type' => $form->getMorphClass(),
                    'related_id' => $form->id,
                    'type' => WorkflowTriggerType::EventBased,
                ])
        )
        ->create();

    $faker = fake();
    $newWorkflowName = $faker->sentence(3);

    livewire(EditWorkflow::class, ['record' => $oldWorkflow->getKey()])
        ->fillForm(['name' => $newWorkflowName])
        ->call('save');

    $oldWorkflow->refresh();

    expect($oldWorkflow->name)->toBe($newWorkflowName);
});

test('can successfully enable and disable workflow for form', function () {
    asSuperAdmin();

    $form = Form::factory()->create();
    $workflow = Workflow::factory()
        ->for(
            WorkflowTrigger::factory()
                ->state([
                    'related_type' => $form->getMorphClass(),
                    'related_id' => $form->id,
                    'type' => WorkflowTriggerType::EventBased,
                ])
        )
        ->state(['is_enabled' => false])
        ->create();

    livewire(EditWorkflow::class, ['record' => $workflow->getKey()])
        ->fillForm(['is_enabled' => true])
        ->call('save');

    $workflow->refresh();
    expect($workflow->is_enabled)->toBeTrue();

    livewire(EditWorkflow::class, ['record' => $workflow->getKey()])
        ->fillForm(['is_enabled' => false])
        ->call('save');

    $workflow->refresh();
    expect($workflow->is_enabled)->toBeFalse();
});

test('validates required workflow name during edit', function () {
    asSuperAdmin();

    $form = Form::factory()->create();
    $workflow = Workflow::factory()
        ->for(
            WorkflowTrigger::factory()
                ->state([
                    'related_type' => $form->getMorphClass(),
                    'related_id' => $form->id,
                ])
        )
        ->create();

    livewire(EditWorkflow::class, ['record' => $workflow->getKey()])
        ->fillForm(['name' => ''])
        ->call('save')
        ->assertHasFormErrors(['name' => 'required']);

    $workflow->refresh();
    expect($workflow->name)->not->toBe('');
});

test('validates workflow name maximum length during edit', function () {
    asSuperAdmin();

    $form = Form::factory()->create();
    $workflow = Workflow::factory()
        ->for(
            WorkflowTrigger::factory()
                ->state([
                    'related_type' => $form->getMorphClass(),
                    'related_id' => $form->id,
                ])
        )
        ->create();

    $longName = str_repeat('a', 256);

    livewire(EditWorkflow::class, ['record' => $workflow->getKey()])
        ->fillForm(['name' => $longName])
        ->call('save')
        ->assertHasFormErrors(['name' => 'max']);

    $workflow->refresh();
    expect($workflow->name)->not->toBe($longName);
});

test('form workflow deletion succeeds with proper permissions', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();
    $form = Form::factory()->create();
    $workflow = Workflow::factory()
        ->for(
            WorkflowTrigger::factory()
                ->state([
                    'related_type' => $form->getMorphClass(),
                    'related_id' => $form->id,
                ])
        )
        ->create();

    $user->givePermissionTo('form.view-any');
    $user->givePermissionTo('form.*.update');
    $user->givePermissionTo('form.*.delete');

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

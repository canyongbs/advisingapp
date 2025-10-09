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

use AdvisingApp\Form\Filament\Resources\FormResource\Pages\ManageFormWorkflows;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\FormField;
use AdvisingApp\Workflow\Enums\WorkflowTriggerType;
use AdvisingApp\Workflow\Filament\Resources\Workflows\Pages\EditWorkflow;
use AdvisingApp\Workflow\Models\Workflow;
use AdvisingApp\Workflow\Models\WorkflowTrigger;
use App\Models\User;
use Filament\Actions\DeleteAction;

use function Pest\Laravel\assertDatabaseHas;
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

    assertDatabaseHas(Workflow::class, [
        'name' => 'Form Workflow',
        'is_enabled' => false,
        'workflow_trigger_id' => $workflowTrigger->id,
    ]);

    assertDatabaseHas(WorkflowTrigger::class, [
        'type' => WorkflowTriggerType::EventBased->value,
        'related_type' => $form->getMorphClass(),
        'related_id' => $form->id,
        'created_by_id' => $user->id,
    ]);
});

test('creates workflow with proper database transaction behavior', function () {
    asSuperAdmin();

    $form = Form::factory()->create();

    expect(Workflow::count())->toBe(0);
    expect(WorkflowTrigger::count())->toBe(0);

    livewire(ManageFormWorkflows::class, ['record' => $form->getKey()])
        ->callAction('create');

    expect(Workflow::count())->toBe(1);
    expect(WorkflowTrigger::count())->toBe(1);
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

test('requires proper authentication to create workflows', function () {
    $form = Form::factory()->create();

    livewire(ManageFormWorkflows::class, ['record' => $form->getKey()])
        ->assertForbidden();

    expect(Workflow::count())->toBe(0);
    expect(WorkflowTrigger::count())->toBe(0);
});

test('authenticated super admin user can create workflows', function () {
    asSuperAdmin();

    $form = Form::factory()->create();

    livewire(ManageFormWorkflows::class, ['record' => $form->getKey()])
        ->callAction('create');

    expect(Workflow::count())->toBe(1);
    expect(WorkflowTrigger::count())->toBe(1);

    $workflowTrigger = WorkflowTrigger::first();
    $user = User::first();
    expect($workflowTrigger->created_by_id)->toBe($user->id);
});

test('creates workflow trigger with correct form relationships', function () {
    asSuperAdmin();

    $form = Form::factory()
        ->has(FormField::factory()->count(3), 'fields')
        ->create();

    livewire(ManageFormWorkflows::class, ['record' => $form->getKey()])
        ->callAction('create');

    $workflowTrigger = WorkflowTrigger::first();
    $relatedForm = $workflowTrigger->related;
    assert($relatedForm instanceof Form);

    expect($relatedForm)->toBeInstanceOf(Form::class);
    expect($relatedForm->id)->toBe($form->id);
    expect($relatedForm->name)->toBe($form->name);
    expect($relatedForm->fields)->toHaveCount(3);
});

test('creates workflow for form with different form configurations', function (callable $formFactory, array $expectedConfig) {
    asSuperAdmin();

    $form = $formFactory();

    livewire(ManageFormWorkflows::class, ['record' => $form->getKey()])
        ->callAction('create');

    expect(Workflow::count())->toBe(1);
    expect(WorkflowTrigger::count())->toBe(1);

    $workflowTrigger = WorkflowTrigger::first();
    expect($workflowTrigger->related_id)->toBe($form->id);

    foreach ($expectedConfig as $attribute => $value) {
        expect($form->{$attribute})->toBe($value);
    }
})->with([
    'embedded form' => [
        fn () => Form::factory()->state(['embed_enabled' => true])->create(),
        ['embed_enabled' => true],
    ],
    'form with description' => [
        fn () => Form::factory()->state(['description' => 'Test form description'])->create(),
        ['description' => 'Test form description'],
    ],
    'form with allowed domains' => [
        fn () => Form::factory()->state(['allowed_domains' => ['example.com', 'test.org']])->create(),
        ['allowed_domains' => ['example.com', 'test.org']],
    ],
]);

test('handles workflow creation gracefully when form does not exist', function () {
    asSuperAdmin();

    $form = Form::factory()->create();
    $formId = $form->id;
    $form->delete();

    expect(function () use ($formId) {
        $nonExistentForm = new Form(['id' => $formId]);
        livewire(ManageFormWorkflows::class, ['record' => $nonExistentForm->getKey()])
            ->callAction('create');
    })->toThrow(Exception::class);

    expect(Workflow::count())->toBe(0);
    expect(WorkflowTrigger::count())->toBe(0);
});

test('creates workflow in disabled state by default for safety', function () {
    asSuperAdmin();

    $form = Form::factory()->create();

    livewire(ManageFormWorkflows::class, ['record' => $form->getKey()])
        ->callAction('create');

    $workflow = Workflow::first();

    expect($workflow->is_enabled)->toBeFalse();

    assertDatabaseHas(Workflow::class, [
        'is_enabled' => false,
    ]);
});

test('creates workflow with event-based trigger type for form submissions', function () {
    asSuperAdmin();

    $form = Form::factory()->create();

    livewire(ManageFormWorkflows::class, ['record' => $form->getKey()])
        ->callAction('create');

    $workflowTrigger = WorkflowTrigger::first();

    expect($workflowTrigger->type)->toBe(WorkflowTriggerType::EventBased);

    assertDatabaseHas(WorkflowTrigger::class, [
        'type' => WorkflowTriggerType::EventBased->value,
    ]);
});

test('workflow creation integrates properly with existing form workflows list', function () {
    asSuperAdmin();

    $form = Form::factory()->create();

    Workflow::factory()
        ->count(2)
        ->for(
            WorkflowTrigger::factory()
                ->state([
                    'related_type' => $form->getMorphClass(),
                    'related_id' => $form->id,
                ])
        )
        ->create();

    expect(Workflow::count())->toBe(2);

    livewire(ManageFormWorkflows::class, ['record' => $form->getKey()])
        ->callAction('create');

    expect(Workflow::count())->toBe(3);

    $allTriggers = WorkflowTrigger::all();

    foreach ($allTriggers as $trigger) {
        expect($trigger->related_id)->toBe($form->id);
    }
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

    $newWorkflow = Workflow::factory()->make();

    livewire(EditWorkflow::class, ['record' => $oldWorkflow->getKey()])
        ->fillForm(['name' => $newWorkflow->name])
        ->call('save');

    $oldWorkflow->refresh();

    expect($oldWorkflow->name)->toBe($newWorkflow->name);

    assertDatabaseHas(Workflow::class, [
        'id' => $oldWorkflow->id,
        'name' => $newWorkflow->name,
    ]);
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

    assertDatabaseHas(Workflow::class, [
        'id' => $workflow->id,
        'is_enabled' => false,
    ]);
});

test('can edit both workflow name and enabled status simultaneously', function () {
    asSuperAdmin();

    $form = Form::factory()->create();
    $oldWorkflow = Workflow::factory()
        ->for(
            WorkflowTrigger::factory()
                ->state([
                    'related_type' => $form->getMorphClass(),
                    'related_id' => $form->id,
                ])
        )
        ->state(['is_enabled' => false])
        ->create();

    $newWorkflow = Workflow::factory()->make();

    livewire(EditWorkflow::class, ['record' => $oldWorkflow->getKey()])
        ->fillForm([
            'name' => $newWorkflow->name,
            'is_enabled' => true,
        ])
        ->call('save');

    $oldWorkflow->refresh();

    expect($oldWorkflow->name)->toBe($newWorkflow->name);
    expect($oldWorkflow->is_enabled)->toBeTrue();

    assertDatabaseHas(Workflow::class, [
        'id' => $oldWorkflow->id,
        'name' => $newWorkflow->name,
        'is_enabled' => true,
    ]);
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

test('authenticated super admin user can edit workflows', function () {
    asSuperAdmin();

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

    $newWorkflow = Workflow::factory()->make();

    livewire(EditWorkflow::class, ['record' => $oldWorkflow->getKey()])
        ->fillForm(['name' => $newWorkflow->name])
        ->call('save');

    $oldWorkflow->refresh();
    expect($oldWorkflow->name)->toBe($newWorkflow->name);
});

test('editing workflow preserves form relationships and trigger data', function () {
    asSuperAdmin();

    $form = Form::factory()
        ->has(FormField::factory()->count(3), 'fields')
        ->create();

    $workflowTrigger = WorkflowTrigger::factory()
        ->state([
            'related_type' => $form->getMorphClass(),
            'related_id' => $form->id,
            'type' => WorkflowTriggerType::EventBased,
        ])
        ->create();

    $oldWorkflow = Workflow::factory()
        ->for($workflowTrigger)
        ->create();

    $newWorkflow = Workflow::factory()->make();

    livewire(EditWorkflow::class, ['record' => $oldWorkflow->getKey()])
        ->fillForm(['name' => $newWorkflow->name])
        ->call('save');

    $oldWorkflow->refresh();
    $workflowTrigger->refresh();

    expect($oldWorkflow->name)->toBe($newWorkflow->name);

    expect($workflowTrigger->related_type)->toBe($form->getMorphClass());
    expect($workflowTrigger->related_id)->toBe($form->id);
    expect($workflowTrigger->type)->toBe(WorkflowTriggerType::EventBased);

    $relatedForm = $workflowTrigger->related;
    assert($relatedForm instanceof Form);
    expect($relatedForm)->toBeInstanceOf(Form::class);
    expect($relatedForm->id)->toBe($form->id);
    expect($relatedForm->fields)->toHaveCount(3);
});

test('editing workflow with different form configurations preserves specific form attributes', function (callable $formFactory, array $expectedConfig) {
    asSuperAdmin();

    $form = $formFactory();
    $oldWorkflow = Workflow::factory()
        ->for(
            WorkflowTrigger::factory()
                ->state([
                    'related_type' => $form->getMorphClass(),
                    'related_id' => $form->id,
                ])
        )
        ->create();

    $newWorkflow = Workflow::factory()->make();

    livewire(EditWorkflow::class, ['record' => $oldWorkflow->getKey()])
        ->fillForm(['name' => $newWorkflow->name])
        ->call('save');

    $oldWorkflow->refresh();
    $form->refresh();

    expect($oldWorkflow->name)->toBe($newWorkflow->name);

    foreach ($expectedConfig as $attribute => $value) {
        expect($form->{$attribute})->toBe($value);
    }
})->with([
    'embedded form' => [
        fn () => Form::factory()->state(['embed_enabled' => true])->create(),
        ['embed_enabled' => true],
    ],
    'form with description' => [
        fn () => Form::factory()->state(['description' => 'Preserved description'])->create(),
        ['description' => 'Preserved description'],
    ],
    'form with allowed domains' => [
        fn () => Form::factory()->state(['allowed_domains' => ['edit.com', 'test.org']])->create(),
        ['allowed_domains' => ['edit.com', 'test.org']],
    ],
]);

test('editing workflow handles database transaction behavior properly', function () {
    asSuperAdmin();

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

    $newWorkflow = Workflow::factory()->make();

    livewire(EditWorkflow::class, ['record' => $oldWorkflow->getKey()])
        ->fillForm(['name' => $newWorkflow->name])
        ->call('save');

    $oldWorkflow->refresh();
    expect($oldWorkflow->name)->toBe($newWorkflow->name);

    assertDatabaseHas(Workflow::class, [
        'id' => $oldWorkflow->id,
        'name' => $newWorkflow->name,
    ]);
});

test('can delete workflow through edit page', function () {
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

    $triggerId = $workflow->workflow_trigger_id;

    livewire(EditWorkflow::class, [
        'record' => $workflow->getRouteKey(),
    ])
        ->assertActionExists(DeleteAction::class)
        ->assertActionEnabled(DeleteAction::class)
        ->callAction(DeleteAction::class);

    assertSoftDeleted($workflow);

    expect(WorkflowTrigger::find($triggerId))->not->toBeNull();
});

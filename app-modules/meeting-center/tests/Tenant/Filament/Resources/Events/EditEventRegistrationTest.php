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

use AdvisingApp\MeetingCenter\Filament\Resources\Events\Pages\EditEventRegistration;
use AdvisingApp\MeetingCenter\Models\Event;
use AdvisingApp\MeetingCenter\Models\EventRegistrationForm;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormField;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormStep;
use App\Settings\LicenseSettings;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

function editEventRegistrationTestSetup(): void
{
    $settings = app(LicenseSettings::class);
    $settings->data->addons->eventManagement = true;
    $settings->save();
}

it('creates a new version and archives the old one when saving the registration form', function () {
    editEventRegistrationTestSetup();

    asSuperAdmin();

    $event = Event::factory()->create();
    $form = $event->eventRegistrationForm;

    $originalId = $form->id;
    $originalRootId = $form->root_id;

    expect($form->archived_at)->toBeNull();
    expect(EventRegistrationForm::withoutGlobalScopes()->where('root_id', $originalRootId)->count())->toBe(1);

    livewire(EditEventRegistration::class, ['record' => $event->getKey()])
        ->call('save')
        ->assertHasNoErrors();

    expect(EventRegistrationForm::withoutGlobalScopes()->where('root_id', $originalRootId)->count())->toBe(2);

    $archivedForm = EventRegistrationForm::withoutGlobalScopes()
        ->where('id', $originalId)
        ->first();

    expect($archivedForm->archived_at)->not->toBeNull();

    $newForm = EventRegistrationForm::withoutGlobalScopes()
        ->where('root_id', $originalRootId)
        ->whereNull('archived_at')
        ->first();

    expect($newForm)->not->toBeNull();
    expect($newForm->id)->not->toBe($originalId);
    expect($newForm->event_id)->toBe($event->getKey());
    expect($newForm->root_id)->toBe($originalRootId);
});

it('the new version inherits embed settings from the old version', function () {
    editEventRegistrationTestSetup();

    asSuperAdmin();

    $event = Event::factory()->create();
    $form = $event->eventRegistrationForm;
    $form->embed_enabled = true;
    $form->allowed_domains = ['example.com'];
    $form->save();

    $originalRootId = $form->root_id;

    livewire(EditEventRegistration::class, ['record' => $event->getKey()])
        ->call('save')
        ->assertHasNoErrors();

    $newForm = EventRegistrationForm::withoutGlobalScopes()
        ->where('root_id', $originalRootId)
        ->whereNull('archived_at')
        ->first();

    expect($newForm->embed_enabled)->toBeTrue();
    expect($newForm->allowed_domains)->toBe(['example.com']);
});

it('the event registration form relationship resolves to the latest non-archived version', function () {
    editEventRegistrationTestSetup();

    asSuperAdmin();

    $event = Event::factory()->create();
    $form = $event->eventRegistrationForm;
    $originalId = $form->id;

    livewire(EditEventRegistration::class, ['record' => $event->getKey()])
        ->call('save')
        ->assertHasNoErrors();

    $event->refresh();
    $currentForm = $event->eventRegistrationForm;

    expect($currentForm)->not->toBeNull();
    expect($currentForm->id)->not->toBe($originalId);
    expect($currentForm->archived_at)->toBeNull();
});

it('sets root_id to its own id when a registration form is first created', function () {
    editEventRegistrationTestSetup();

    $event = Event::factory()->create();
    $form = $event->eventRegistrationForm;

    expect($form->root_id)->toBe($form->id);
});

it('when saving a wizard registration form, the new version retains the same number of steps', function () {
    editEventRegistrationTestSetup();

    asSuperAdmin();

    $event = Event::factory()->create();
    $form = $event->eventRegistrationForm;

    // Ensure the form is in wizard mode with a known set of steps
    $form->steps()->delete();
    $form->is_wizard = true;
    $form->content = null;
    $form->save();

    EventRegistrationFormStep::factory()->count(2)->create(['form_id' => $form->getKey()]);

    $originalStepCount = $form->steps()->count();
    $originalRootId = $form->root_id;
    $originalId = $form->id;

    livewire(EditEventRegistration::class, ['record' => $event->getKey()])
        ->call('save')
        ->assertHasNoErrors();

    $newVersion = EventRegistrationForm::withoutGlobalScopes()
        ->where('root_id', $originalRootId)
        ->whereNull('archived_at')
        ->first();

    expect($newVersion)->not->toBeNull();
    expect($newVersion->id)->not->toBe($originalId);
    expect($newVersion->is_wizard)->toBeTrue();
    expect($newVersion->steps()->count())->toBe($originalStepCount);

    // Archived version retains its original steps
    $archivedVersion = EventRegistrationForm::withoutGlobalScopes()
        ->where('id', $originalId)
        ->first();

    expect($archivedVersion->archived_at)->not->toBeNull();
    expect($archivedVersion->steps()->count())->toBe($originalStepCount);
});

it('when saving a wizard registration form, the archived version still has its original steps', function () {
    editEventRegistrationTestSetup();

    asSuperAdmin();

    $event = Event::factory()->create();
    $form = $event->eventRegistrationForm;

    $form->steps()->delete();
    $form->is_wizard = true;
    $form->content = null;
    $form->save();

    EventRegistrationFormStep::factory()->count(3)->create(['form_id' => $form->getKey()]);

    $originalStepCount = $form->steps()->count();
    $originalId = $form->id;

    livewire(EditEventRegistration::class, ['record' => $event->getKey()])
        ->call('save')
        ->assertHasNoErrors();

    $archivedVersion = EventRegistrationForm::withoutGlobalScopes()
        ->where('id', $originalId)
        ->first();

    expect($archivedVersion->archived_at)->not->toBeNull();
    expect($archivedVersion->steps()->count())->toBe($originalStepCount);
});

it('carries each wizard step its own fields onto the new version when saving', function () {
    editEventRegistrationTestSetup();

    asSuperAdmin();

    $event = Event::factory()->create();
    $form = $event->eventRegistrationForm;

    $form->steps()->delete();
    $form->fields()->delete();
    $form->is_wizard = true;
    $form->content = null;
    $form->save();

    foreach ([0, 1] as $index) {
        $step = $form->steps()->create(['label' => "Step {$index}", 'sort' => $index]);
        $field = EventRegistrationFormField::factory()->create([
            'form_id' => $form->getKey(),
            'step_id' => $step->getKey(),
        ]);
        $step->content = [
            'type' => 'doc',
            'content' => [[
                'type' => 'customBlock',
                'attrs' => ['id' => $field->type, 'config' => [
                    'fieldId' => $field->getKey(),
                    'label' => $field->label,
                    'isRequired' => $field->is_required,
                ]],
            ]],
        ];
        $step->save();
    }

    $originalRootId = $form->root_id;
    $originalFieldIds = $form->fields()->pluck('id');

    livewire(EditEventRegistration::class, ['record' => $event->getKey()])
        ->call('save')
        ->assertHasNoErrors();

    $newVersion = EventRegistrationForm::withoutGlobalScopes()
        ->where('root_id', $originalRootId)
        ->whereNull('archived_at')
        ->first();

    expect($newVersion->fields()->count())->toBe(2);
    expect($newVersion->fields()->whereIn('id', $originalFieldIds)->count())->toBe(0);

    $newVersion->steps()->orderBy('sort')->get()->each(function (EventRegistrationFormStep $step) use ($newVersion) {
        expect($newVersion->fields()->where('step_id', $step->getKey())->count())->toBe(1);

        $fieldId = data_get($step->content, 'content.0.attrs.config.fieldId');
        expect($newVersion->fields()->whereKey($fieldId)->exists())->toBeTrue();
    });
});

it('persists a newly added wizard step and its fields to the new version', function () {
    editEventRegistrationTestSetup();

    asSuperAdmin();

    $event = Event::factory()->create();
    $form = $event->eventRegistrationForm;

    $form->steps()->delete();
    $form->fields()->delete();
    $form->is_wizard = true;
    $form->content = null;
    $form->save();

    EventRegistrationFormStep::factory()
        ->count(2)
        ->sequence(fn ($sequence) => ['sort' => $sequence->index, 'label' => "Existing {$sequence->index}"])
        ->create(['form_id' => $form->getKey()]);

    $originalRootId = $form->root_id;

    $component = livewire(EditEventRegistration::class, ['record' => $event->getKey()]);

    $steps = data_get($component->instance()->form->getRawState(), 'eventRegistrationForm.steps', []);
    $steps['newStepKey'] = [
        'label' => 'Brand New Step',
        'content' => [
            'type' => 'doc',
            'content' => [[
                'type' => 'customBlock',
                'attrs' => ['id' => 'text_input', 'config' => ['label' => 'New Field', 'isRequired' => false]],
            ]],
        ],
    ];

    $component
        ->fillForm(['eventRegistrationForm' => ['steps' => $steps]])
        ->call('save')
        ->assertHasNoErrors();

    $newVersion = EventRegistrationForm::withoutGlobalScopes()
        ->where('root_id', $originalRootId)
        ->whereNull('archived_at')
        ->first();

    expect($newVersion->steps()->count())->toBe(3);
    expect($newVersion->steps()->pluck('label'))->toContain('Brand New Step');

    $newStep = $newVersion->steps()->where('label', 'Brand New Step')->first();

    expect($newStep)->not->toBeNull();
    expect($newVersion->fields()->where('step_id', $newStep->getKey())->count())->toBe(1);
});

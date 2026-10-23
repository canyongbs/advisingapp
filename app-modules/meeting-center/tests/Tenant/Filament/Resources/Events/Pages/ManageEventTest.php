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

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Form\Enums\Rounding;
use AdvisingApp\Form\Filament\Blocks\FormFieldBlockRegistry;
use AdvisingApp\MeetingCenter\Filament\Resources\Events\EventResource;
use AdvisingApp\MeetingCenter\Filament\Resources\Events\Pages\ManageEvent;
use AdvisingApp\MeetingCenter\Filament\Resources\Events\RelationManagers\EventAttendeesRelationManager;
use AdvisingApp\MeetingCenter\Models\Event;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use AdvisingApp\MeetingCenter\Models\EventRegistrationForm;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormField;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormStep;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Actions\Testing\TestAction;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Tests\asSuperAdmin;

beforeEach(function (): void {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->eventManagement = true;
    $settings->save();
});

$actingAsUserWithPermissions = function (string ...$permissions): User {
    $user = User::factory()->licensed(LicenseType::cases())->create();
    $user->givePermissionTo('event.view-any', ...$permissions);
    actingAs($user);

    return $user;
};

$getTabLabels = function (Testable $component): array {
    $document = new DOMDocument();
    $document->loadHTML($component->html(), LIBXML_NOERROR | LIBXML_NOWARNING);
    $tabs = (new DOMXPath($document))->query('//*[@role="tab"]');
    assert($tabs !== false);

    $labels = [];

    foreach ($tabs as $tab) {
        $labels[] = trim((string) preg_replace('/\s+/', ' ', $tab->textContent));
    }

    return $labels;
};

$getRenderedFormIds = function (Testable $component): array {
    $document = new DOMDocument();
    $document->loadHTML($component->html(), LIBXML_NOERROR | LIBXML_NOWARNING);
    $forms = $document->getElementsByTagName('form');

    $ids = [];

    foreach ($forms as $form) {
        $ids[] = $form->getAttribute('id');
    }

    return $ids;
};

$archiveAction = fn (): TestAction => TestAction::make('archive')->schemaComponent('relationManagerTabs.details.form-actions', schema: 'content');

$getNewRegistrationFormVersion = fn (EventRegistrationForm $form): EventRegistrationForm => EventRegistrationForm::withoutGlobalScopes()
    ->where('root_id', $form->root_id)
    ->whereNull('archived_at')
    ->firstOrFail();

$makeWizard = function (Event $event): EventRegistrationForm {
    $form = $event->eventRegistrationForm;
    $form->steps()->delete();
    $form->is_wizard = true;
    $form->content = null;
    $form->save();

    return $form;
};

it('can render the manage event page', function (): void {
    asSuperAdmin();

    $event = Event::factory()->create();

    get(EventResource::getUrl('view', ['record' => $event]))
        ->assertSuccessful();
});

describe('tabs', function () use ($actingAsUserWithPermissions, $getTabLabels, $getRenderedFormIds): void {
    it('renders the attendees relation manager only on its own tab', function (): void {
        asSuperAdmin();

        $event = Event::factory()->create();

        get(EventResource::getUrl('view', ['record' => $event]))
            ->assertDontSeeLivewire(EventAttendeesRelationManager::class);

        get(EventResource::getUrl('view', ['record' => $event, 'tab' => 'attendees']))
            ->assertSeeLivewire(EventAttendeesRelationManager::class);
    });

    it('keeps unsaved edits when switching tabs', function (): void {
        asSuperAdmin();

        $event = Event::factory()->create();

        Livewire::test(ManageEvent::class, ['record' => $event->getRouteKey()])
            ->set('activeRelationManager', 'details')
            ->fillForm(['title' => 'Unsaved title'])
            ->set('activeRelationManager', 'attendees')
            ->set('activeRelationManager', 'details')
            ->assertSchemaStateSet(['title' => 'Unsaved title']);
    });

    it('only lists the tabs the user can access', function (array $permissions, array $tabs) use ($actingAsUserWithPermissions, $getTabLabels): void {
        $actingAsUserWithPermissions(...$permissions);

        $event = Event::factory()->create();

        expect($getTabLabels(Livewire::test(ManageEvent::class, ['record' => $event->getRouteKey()])))->toBe($tabs);
    })->with([
        'view' => [['event.*.view'], ['Overview']],
        'update' => [['event.*.view', 'event.*.update'], ['Overview', 'Details', 'Landing Page', 'Registration Form']],
        'attendees' => [['event.*.view', 'event_attendee.view-any'], ['Overview', 'Attendees']],
        'update without view' => [['event.*.update'], ['Details', 'Landing Page', 'Registration Form']],
        'attendees without view' => [['event_attendee.view-any'], ['Attendees']],
    ]);

    it('renders the form of the selected tab only', function (?string $tab, array $formIds) use ($getRenderedFormIds): void {
        asSuperAdmin();

        $event = Event::factory()->create();

        $component = Livewire::withQueryParams(['tab' => $tab])
            ->test(ManageEvent::class, ['record' => $event->getRouteKey()]);

        expect($getRenderedFormIds($component))->toBe($formIds);
    })->with([
        'overview' => [null, []],
        'details' => ['details', ['form']],
        'landing page' => ['landing-page', ['landingPageForm']],
        'registration form' => ['registration-form', ['registrationForm']],
    ]);

    it('does not render a tab the user cannot access', function (string $tab) use ($actingAsUserWithPermissions, $getRenderedFormIds): void {
        $actingAsUserWithPermissions('event.*.view');

        $event = Event::factory()->create();

        $component = Livewire::withQueryParams(['tab' => $tab])
            ->test(ManageEvent::class, ['record' => $event->getRouteKey()]);

        expect($getRenderedFormIds($component))->toBe([]);
    })->with(['details', 'landing-page', 'registration-form']);
});

describe('details tab', function () use ($archiveAction): void {
    it('does not allow updating an event to a title matching another non-deleted event case-insensitively', function (): void {
        asSuperAdmin();

        Event::factory()->create(['title' => 'Other Event']);
        $event = Event::factory()->create(['title' => 'Editable Event']);

        Livewire::test(ManageEvent::class, ['record' => $event->getRouteKey()])
            ->fillForm(['title' => 'other event'])
            ->call('save')
            ->assertHasFormErrors(['title' => 'unique']);
    });

    it('allows updating an event to a title freed up by a soft-deleted event case-insensitively', function (): void {
        asSuperAdmin();

        $deletedEvent = Event::factory()->create(['title' => 'Reusable Title']);
        $deletedEvent->delete();

        $event = Event::factory()->create(['title' => 'Editable Event']);

        Livewire::test(ManageEvent::class, ['record' => $event->getRouteKey()])
            ->fillForm(['title' => 'reusable title'])
            ->call('save')
            ->assertHasNoFormErrors();

        expect($event->fresh()->title)->toBe('reusable title');
    });

    it('saves the registration form settings alongside the event', function (): void {
        asSuperAdmin();

        $event = Event::factory()->create();
        $event->eventRegistrationForm->update([
            'embed_enabled' => false,
            'allowed_domains' => [],
            'primary_color' => 'blue',
            'rounding' => Rounding::None,
        ]);

        Livewire::test(ManageEvent::class, ['record' => $event->getRouteKey()])
            ->fillForm([
                'eventRegistrationForm' => [
                    'embed_enabled' => true,
                    'allowed_domains' => ['example.com'],
                    'primary_color' => 'red',
                    'rounding' => Rounding::Full,
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified()
            ->assertRedirect(EventResource::getUrl('view', ['record' => $event, 'tab' => 'details']));

        expect($event->eventRegistrationForm->fresh())
            ->embed_enabled->toBeTrue()
            ->allowed_domains->toBe(['example.com'])
            ->primary_color->toBe('red')
            ->rounding->toBe(Rounding::Full);
    });

    it('shows the `Archive` action whether or not the event has attendees', function (bool $hasAttendees) use ($archiveAction): void {
        asSuperAdmin();

        $event = Event::factory()->create(['starts_at' => now()->addWeek()]);
        $event->attendees()->delete();

        if ($hasAttendees) {
            EventAttendee::factory()->create(['event_id' => $event->id]);
        }

        Livewire::test(ManageEvent::class, ['record' => $event->getRouteKey()])
            ->assertActionVisible($archiveAction())
            ->assertActionHasLabel($archiveAction(), 'Archive');
    })->with([
        'with attendees' => true,
        'without attendees' => false,
    ]);

    it('archives the event and redirects to the index', function () use ($archiveAction): void {
        asSuperAdmin();

        $event = Event::factory()->create(['starts_at' => now()->addWeek()]);
        EventAttendee::factory()->create(['event_id' => $event->id]);

        expect($event->isArchived())->toBeFalse();

        Livewire::test(ManageEvent::class, ['record' => $event->getRouteKey()])
            ->callAction($archiveAction())
            ->assertRedirect(EventResource::getUrl('index'));

        expect($event->fresh()->isArchived())->toBeTrue();
    });
});

describe('landing page tab', function (): void {
    it('saves the rich description and hero image onto the event', function (): void {
        Storage::fake('s3-public');

        asSuperAdmin();

        $event = Event::factory()->create(['description' => null]);

        expect($event->getFirstMedia('hero_image'))->toBeNull();

        $description = ['type' => 'doc', 'content' => [['type' => 'paragraph', 'content' => [['type' => 'text', 'text' => 'Join us for this event.']]]]];

        Livewire::test(ManageEvent::class, ['record' => $event->getRouteKey()])
            ->fillForm([
                'description' => $description,
                'hero_image' => UploadedFile::fake()->image('hero.png'),
            ], 'landingPageForm')
            ->call('saveLandingPage')
            ->assertHasNoFormErrors(form: 'landingPageForm')
            ->assertNotified();

        $event = $event->fresh();

        expect($event->description)->toEqual($description)
            ->and($event->getFirstMedia('hero_image'))->not->toBeNull();
    });
});

describe('registration form tab', function () use ($getNewRegistrationFormVersion, $makeWizard): void {
    it('creates a new version and archives the old one', function () use ($getNewRegistrationFormVersion): void {
        asSuperAdmin();

        $event = Event::factory()->create();
        $form = $event->eventRegistrationForm;
        $form->embed_enabled = true;
        $form->allowed_domains = ['example.com'];
        $form->save();

        expect(EventRegistrationForm::withoutGlobalScopes()->where('root_id', $form->root_id)->count())->toBe(1);

        Livewire::test(ManageEvent::class, ['record' => $event->getRouteKey()])
            ->call('saveRegistrationForm')
            ->assertHasNoErrors()
            ->assertRedirect(EventResource::getUrl('view', ['record' => $event, 'tab' => 'registration-form']));

        $newVersion = $getNewRegistrationFormVersion($form);

        expect(EventRegistrationForm::withoutGlobalScopes()->where('root_id', $form->root_id)->count())->toBe(2)
            ->and($form->fresh()->archived_at)->not->toBeNull()
            ->and($newVersion->getKey())->not->toBe($form->getKey())
            ->and($newVersion->event_id)->toBe($event->getKey())
            ->and($newVersion->embed_enabled)->toBeTrue()
            ->and($newVersion->allowed_domains)->toBe(['example.com'])
            ->and($event->fresh()->eventRegistrationForm->getKey())->toBe($newVersion->getKey());
    });

    it('keeps saved details settings when the registration form is saved afterwards', function () use ($getNewRegistrationFormVersion): void {
        asSuperAdmin();

        $event = Event::factory()->create();
        $form = $event->eventRegistrationForm;
        $form->update(['embed_enabled' => false, 'allowed_domains' => []]);

        Livewire::test(ManageEvent::class, ['record' => $event->getRouteKey()])
            ->fillForm(['eventRegistrationForm' => ['embed_enabled' => true, 'allowed_domains' => ['example.com']]])
            ->call('save')
            ->assertHasNoFormErrors()
            ->call('saveRegistrationForm')
            ->assertHasNoErrors();

        expect($getNewRegistrationFormVersion($form))
            ->embed_enabled->toBeTrue()
            ->allowed_domains->toBe(['example.com']);
    });

    it('sets root_id to its own id when a registration form is first created', function (): void {
        $event = Event::factory()->create();
        $form = $event->eventRegistrationForm;

        expect($form->root_id)->toBe($form->id);
    });

    it('can add a new step to a multi-step registration form', function () use ($makeWizard): void {
        asSuperAdmin();

        $event = Event::factory()->create();
        $form = $makeWizard($event);
        $form->submissions()->delete();

        $component = Livewire::test(ManageEvent::class, ['record' => $event->getRouteKey()]);
        $page = $component->instance();
        assert($page instanceof ManageEvent);

        $stepsBefore = count(data_get($page->registrationForm->getRawState(), 'eventRegistrationForm.steps', []));

        $component
            ->callAction(TestAction::make('add')->schemaComponent('eventRegistrationForm.steps', schema: 'registrationForm'))
            ->assertHasNoErrors();

        $page = $component->instance();
        assert($page instanceof ManageEvent);

        expect(data_get($page->registrationForm->getRawState(), 'eventRegistrationForm.steps', []))
            ->toHaveCount($stepsBefore + 1);
    });

    it('saves edits to an existing wizard step onto the new version', function () use ($getNewRegistrationFormVersion, $makeWizard): void {
        asSuperAdmin();

        $event = Event::factory()->create();
        $form = $makeWizard($event);
        $form->steps()->create([
            'label' => 'Original label',
            'description' => 'Original description',
            'content' => ['type' => 'doc', 'content' => []],
        ]);

        $component = Livewire::test(ManageEvent::class, ['record' => $event->getRouteKey()]);
        $page = $component->instance();
        assert($page instanceof ManageEvent);

        $steps = data_get($page->registrationForm->getRawState(), 'eventRegistrationForm.steps', []);
        $stepKey = array_key_first($steps);
        $steps[$stepKey]['label'] = 'Updated label';
        $steps[$stepKey]['description'] = 'Updated description';

        $component
            ->fillForm(['eventRegistrationForm' => ['steps' => $steps]], 'registrationForm')
            ->call('saveRegistrationForm')
            ->assertHasNoErrors();

        expect($getNewRegistrationFormVersion($form)->steps()->first())
            ->label->toBe('Updated label')
            ->description->toBe('Updated description');
    });

    it('keeps every wizard step on both versions when saving', function (int $stepCount) use ($getNewRegistrationFormVersion, $makeWizard): void {
        asSuperAdmin();

        $event = Event::factory()->create();
        $form = $makeWizard($event);
        EventRegistrationFormStep::factory()->count($stepCount)->create(['form_id' => $form->getKey()]);

        Livewire::test(ManageEvent::class, ['record' => $event->getRouteKey()])
            ->call('saveRegistrationForm')
            ->assertHasNoErrors();

        $newVersion = $getNewRegistrationFormVersion($form);

        expect($newVersion->is_wizard)->toBeTrue()
            ->and($newVersion->steps()->count())->toBe($stepCount)
            ->and($form->fresh()->archived_at)->not->toBeNull()
            ->and($form->steps()->count())->toBe($stepCount);
    })->with([2, 3]);

    it('carries each wizard step its own fields onto the new version when saving', function () use ($getNewRegistrationFormVersion, $makeWizard): void {
        asSuperAdmin();

        $event = Event::factory()->create();
        $form = $makeWizard($event);
        $form->fields()->delete();

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

        $originalFieldIds = $form->fields()->pluck('id');

        Livewire::test(ManageEvent::class, ['record' => $event->getRouteKey()])
            ->call('saveRegistrationForm')
            ->assertHasNoErrors();

        $newVersion = $getNewRegistrationFormVersion($form);

        expect($newVersion->fields()->count())->toBe(2)
            ->and($newVersion->fields()->whereIn('id', $originalFieldIds)->count())->toBe(0);

        $newVersion->steps()->orderBy('sort')->get()->each(function (EventRegistrationFormStep $step) use ($newVersion): void {
            expect($newVersion->fields()->where('step_id', $step->getKey())->count())->toBe(1)
                ->and($newVersion->fields()->whereKey(data_get($step->content, 'content.0.attrs.config.fieldId'))->exists())->toBeTrue();
        });
    });

    it('saves a newly added wizard step and its fields to the new version', function () use ($getNewRegistrationFormVersion, $makeWizard): void {
        asSuperAdmin();

        $event = Event::factory()->create();
        $form = $makeWizard($event);
        $form->fields()->delete();

        EventRegistrationFormStep::factory()
            ->count(2)
            ->sequence(fn ($sequence): array => ['sort' => $sequence->index, 'label' => "Existing {$sequence->index}"])
            ->create(['form_id' => $form->getKey()]);

        $component = Livewire::test(ManageEvent::class, ['record' => $event->getRouteKey()]);
        $page = $component->instance();
        assert($page instanceof ManageEvent);

        $steps = data_get($page->registrationForm->getRawState(), 'eventRegistrationForm.steps', []);
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
            ->fillForm(['eventRegistrationForm' => ['steps' => $steps]], 'registrationForm')
            ->call('saveRegistrationForm')
            ->assertHasNoErrors();

        $newVersion = $getNewRegistrationFormVersion($form);
        $newStep = $newVersion->steps()->where('label', 'Brand New Step')->firstOrFail();

        expect($newVersion->steps()->count())->toBe(3)
            ->and($newVersion->fields()->where('step_id', $newStep->getKey())->count())->toBe(1);
    });

    it('exposes the mapped block types to the fields rich editor for the custom block badges', function (): void {
        asSuperAdmin();

        $event = Event::factory()->create();

        Livewire::withQueryParams(['tab' => 'registration-form'])
            ->test(ManageEvent::class, ['record' => $event->getRouteKey()])
            ->assertSeeHtml('data-mapped-block-types="' . implode(',', FormFieldBlockRegistry::getMappedBlockTypes()) . '"');
    });
});

describe('authorization', function () use ($actingAsUserWithPermissions, $archiveAction): void {
    it('denies access to the manage event page without access to any tab', function () use ($actingAsUserWithPermissions): void {
        $actingAsUserWithPermissions();

        $event = Event::factory()->create();

        get(EventResource::getUrl('view', ['record' => $event]))
            ->assertForbidden();
    });

    it('allows access to the manage event page with the `event.*.view` permission', function () use ($actingAsUserWithPermissions): void {
        $actingAsUserWithPermissions('event.*.view');

        $event = Event::factory()->create();

        get(EventResource::getUrl('view', ['record' => $event]))
            ->assertSuccessful();
    });

    it('allows access to the manage event page without the `event.*.view` permission when another tab is accessible', function (string $permission) use ($actingAsUserWithPermissions): void {
        $actingAsUserWithPermissions($permission);

        $event = Event::factory()->create();

        get(EventResource::getUrl('view', ['record' => $event]))
            ->assertSuccessful();
    })->with(['event.*.update', 'event_attendee.view-any']);

    it('does not load the editable forms without the `event.*.update` permission', function () use ($actingAsUserWithPermissions): void {
        $actingAsUserWithPermissions('event.*.view');

        $event = Event::factory()->create();

        Livewire::test(ManageEvent::class, ['record' => $event->getRouteKey()])
            ->assertSet('data', [])
            ->assertSet('landingPageData', [])
            ->assertSet('registrationFormData', []);
    });

    it('does not save without the `event.*.update` permission', function (string $method) use ($actingAsUserWithPermissions): void {
        $user = $actingAsUserWithPermissions('event.*.view', 'event.*.update');

        $event = Event::factory()->create(['title' => 'Original title']);
        $form = $event->eventRegistrationForm;

        $component = Livewire::test(ManageEvent::class, ['record' => $event->getRouteKey()])
            ->fillForm(['title' => 'Renamed']);

        $user->revokePermissionTo('event.*.update');

        $component->call($method)->assertForbidden();

        expect($event->fresh()->title)->toBe('Original title')
            ->and(EventRegistrationForm::withoutGlobalScopes()->where('root_id', $form->root_id)->count())->toBe(1);
    })->with(['save', 'saveLandingPage', 'saveRegistrationForm']);

    it('hides the `Archive` action without the `event.*.delete` permission', function () use ($actingAsUserWithPermissions, $archiveAction): void {
        $actingAsUserWithPermissions('event.*.view', 'event.*.update');

        $event = Event::factory()->create();

        Livewire::test(ManageEvent::class, ['record' => $event->getRouteKey()])
            ->assertActionDoesNotExist($archiveAction());
    });
});

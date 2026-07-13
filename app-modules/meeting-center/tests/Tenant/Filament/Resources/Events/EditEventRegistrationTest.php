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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

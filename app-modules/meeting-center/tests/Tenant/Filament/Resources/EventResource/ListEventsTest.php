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

use AdvisingApp\MeetingCenter\Filament\Resources\EventResource\Pages\ListEvents;
use AdvisingApp\MeetingCenter\Models\Event;
use AdvisingApp\MeetingCenter\Models\EventRegistrationForm;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormSubmission;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('can duplicate a event its registration form its steps and its fields', function () {
    asSuperAdmin();

    // Given that we have an event
    $event = Event::factory()->create();

    expect(Event::count())->toBe(1);
    expect(EventRegistrationForm::count())->toBe(1);

    // And we duplicate it
    livewire(ListEvents::class)
        ->assertStatus(200)
        ->removeTableFilter('pastEvents')
        ->callTableAction('Duplicate', $event);

    // The event, registration form, along with all of its content, should be duplicated
    expect(Event::count())->toBe(2);
    expect(EventRegistrationForm::count())->toBe(2);

    $duplicatedEvent = Event::where('id', '<>', $event->id)->first();

    expect($duplicatedEvent->title)->toBe("Copy - {$event->title}");
    expect($duplicatedEvent->eventRegistrationForm->fields->count())->toBe($event->eventRegistrationForm->fields->count());
    expect($duplicatedEvent->eventRegistrationForm->steps->count())->toBe($event->eventRegistrationForm->steps->count());
});

it('will not duplicate event registration form submissions if they exist', function () {
    asSuperAdmin();

    // Given that we have an event with registration form submissions
    $event = Event::factory()->create();

    $submissionCount = $event->eventRegistrationForm->submissions()->count();

    // And we duplicate it
    livewire(ListEvents::class)
        ->assertStatus(200)
        ->removeTableFilter('pastEvents')
        ->callTableAction('Duplicate', $event);

    // The event registration form submissions should not be duplicated
    expect(EventRegistrationFormSubmission::count())->toBe($submissionCount);

    $duplicatedEvent = Event::where('id', '<>', $event->id)->first();

    expect($duplicatedEvent->eventRegistrationForm->submissions()->count())->toBe(0);
});

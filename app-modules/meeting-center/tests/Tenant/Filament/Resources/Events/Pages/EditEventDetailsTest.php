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

use AdvisingApp\MeetingCenter\Filament\Resources\Events\EventResource;
use AdvisingApp\MeetingCenter\Filament\Resources\Events\Pages\EditEventDetails;
use AdvisingApp\MeetingCenter\Filament\Resources\Events\Pages\ListEvents;
use AdvisingApp\MeetingCenter\Models\Event;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use App\Features\EventArchivingFeature;

use function Pest\Laravel\assertSoftDeleted;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('shows the archive action and hides the delete action in the header when the event has attendees', function () {
    EventArchivingFeature::activate();

    asSuperAdmin();

    $event = Event::factory()->create();

    EventAttendee::factory()->create([
        'event_id' => $event->id,
    ]);

    livewire(EditEventDetails::class, ['record' => $event->getRouteKey()])
        ->assertActionVisible('archive')
        ->assertActionHidden('delete');
});

it('shows the delete action and hides the archive action in the header when the event has no attendees', function () {
    EventArchivingFeature::activate();

    asSuperAdmin();

    $event = Event::factory()->create();
    $event->attendees()->delete();

    livewire(EditEventDetails::class, ['record' => $event->getRouteKey()])
        ->assertActionHidden('archive')
        ->assertActionVisible('delete');
});

it('archive action archives the event and redirects to the index when the event has attendees', function () {
    EventArchivingFeature::activate();

    asSuperAdmin();

    $event = Event::factory()->create();

    EventAttendee::factory()->create([
        'event_id' => $event->id,
    ]);

    livewire(EditEventDetails::class, ['record' => $event->getRouteKey()])
        ->callAction('archive')
        ->assertRedirect(EventResource::getUrl('index'));

    expect($event->fresh()->isArchived())->toBeTrue();
});

it('delete action deletes the event and redirects to the index when the event has no attendees', function () {
    EventArchivingFeature::activate();

    asSuperAdmin();

    $event = Event::factory()->create();
    $event->attendees()->delete();

    livewire(EditEventDetails::class, ['record' => $event->getRouteKey()])
        ->callAction('delete')
        ->assertRedirect(ListEvents::getUrl());

    assertSoftDeleted($event);
});

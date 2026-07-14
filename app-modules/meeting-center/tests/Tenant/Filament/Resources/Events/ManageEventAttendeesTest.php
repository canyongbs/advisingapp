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
use AdvisingApp\MeetingCenter\Filament\Resources\Events\Pages\ManageEventAttendees;
use AdvisingApp\MeetingCenter\Models\Event;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

test('archive action is visible when attendee is not archived', function () {
    asSuperAdmin();

    $event = Event::factory()->create();
    $attendee = EventAttendee::factory()->create(['event_id' => $event->id]);

    livewire(ManageEventAttendees::class, ['record' => $event->getRouteKey()])
        ->assertTableActionVisible('archive', $attendee);
});

test('archive action successfully archives an attendee', function () {
    asSuperAdmin();

    $event = Event::factory()->create();
    $attendee = EventAttendee::factory()->create(['event_id' => $event->id]);

    expect($attendee->isArchived())->toBeFalse();

    livewire(ManageEventAttendees::class, ['record' => $event->getRouteKey()])
        ->callTableAction('archive', $attendee)
        ->assertNotified();

    expect($attendee->fresh()->isArchived())->toBeTrue();
});

test('bulk archive action successfully archives multiple attendees', function () {
    asSuperAdmin();

    $event = Event::factory()->create();
    $attendees = EventAttendee::factory()->count(3)->create(['event_id' => $event->id]);

    $attendees->each(function (EventAttendee $attendee): void {
        expect($attendee->isArchived())->toBeFalse();
    });

    livewire(ManageEventAttendees::class, ['record' => $event->getRouteKey()])
        ->callTableBulkAction('archive', $attendees)
        ->assertNotified();

    $attendees->each(function (EventAttendee $attendee): void {
        expect($attendee->fresh()->isArchived())->toBeTrue();
    });
});

// test('archived attendees are hidden by default', function () {
//     asSuperAdmin();

//     $event = Event::factory()->create();
//     $activeAttendee = EventAttendee::factory()->create(['event_id' => $event->id]);
//     $archivedAttendee = EventAttendee::factory()->create(['event_id' => $event->id, 'archived_at' => now()]);

//     livewire(ManageEventAttendees::class, ['record' => $event->getRouteKey()])
//         ->assertCanSeeTableRecords([$activeAttendee])
//         ->assertCanNotSeeTableRecords([$archivedAttendee]);
// });

test('archived attendees are visible when the withoutArchived filter is removed', function () {
    asSuperAdmin();

    $event = Event::factory()->create();
    $activeAttendee = EventAttendee::factory()->create(['event_id' => $event->id]);
    $archivedAttendee = EventAttendee::factory()->create(['event_id' => $event->id, 'archived_at' => now()]);

    livewire(ManageEventAttendees::class, ['record' => $event->getRouteKey()])
        ->removeTableFilter('withoutArchived')
        ->assertCanSeeTableRecords([$activeAttendee, $archivedAttendee]);
});

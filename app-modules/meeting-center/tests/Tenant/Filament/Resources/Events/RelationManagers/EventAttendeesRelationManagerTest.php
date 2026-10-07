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
use AdvisingApp\MeetingCenter\Filament\Resources\Events\Pages\ViewEvent;
use AdvisingApp\MeetingCenter\Filament\Resources\Events\RelationManagers\EventAttendeesRelationManager;
use AdvisingApp\MeetingCenter\Jobs\CreateEventAttendees;
use AdvisingApp\MeetingCenter\Models\Event;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use App\Models\User;
use App\Settings\LicenseSettings;
use Filament\Actions\Testing\TestAction;
use Illuminate\Support\Facades\Bus;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

$eventAttendeesRelationManagerTestUser = function (): User {
    $settings = app(LicenseSettings::class);
    $settings->data->addons->eventManagement = true;
    $settings->save();
    $user = User::factory()->licensed(LicenseType::cases())->create();
    $user->givePermissionTo(['event.view-any', 'event.*.view']);

    return $user;
};

test('archive action is visible when attendee is not archived', function () {
    asSuperAdmin();

    $event = Event::factory()->create();
    $attendee = EventAttendee::factory()->create(['event_id' => $event->id]);

    livewire(EventAttendeesRelationManager::class, ['ownerRecord' => $event, 'pageClass' => ViewEvent::class])
        ->assertTableActionVisible('archive', $attendee);
});

test('archive action successfully archives an attendee', function () {
    asSuperAdmin();

    $event = Event::factory()->create();
    $attendee = EventAttendee::factory()->create(['event_id' => $event->id]);

    expect($attendee->isArchived())->toBeFalse();

    livewire(EventAttendeesRelationManager::class, ['ownerRecord' => $event, 'pageClass' => ViewEvent::class])
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

    livewire(EventAttendeesRelationManager::class, ['ownerRecord' => $event, 'pageClass' => ViewEvent::class])
        ->callTableBulkAction('archive', $attendees)
        ->assertNotified();

    $attendees->each(function (EventAttendee $attendee): void {
        expect($attendee->fresh()->isArchived())->toBeTrue();
    });
});

test('archived attendees are hidden by default', function () {
    asSuperAdmin();

    $event = Event::factory()->create();
    $event->attendees()->delete();

    $activeAttendee = EventAttendee::factory()->create(['event_id' => $event->id]);
    $archivedAttendee = EventAttendee::factory()->create(['event_id' => $event->id, 'archived_at' => now()]);

    livewire(EventAttendeesRelationManager::class, ['ownerRecord' => $event, 'pageClass' => ViewEvent::class])
        ->loadTable()
        ->assertCanSeeTableRecords([$activeAttendee])
        ->assertCanNotSeeTableRecords([$archivedAttendee]);
});

test('archived attendees are visible when the withoutArchived filter is removed', function () {
    asSuperAdmin();

    $event = Event::factory()->create();
    $event->attendees()->delete();

    $activeAttendee = EventAttendee::factory()->create(['event_id' => $event->id]);
    $archivedAttendee = EventAttendee::factory()->create(['event_id' => $event->id, 'archived_at' => now()]);

    livewire(EventAttendeesRelationManager::class, ['ownerRecord' => $event, 'pageClass' => ViewEvent::class])
        ->loadTable()
        ->removeTableFilter('withoutArchived')
        ->assertCanSeeTableRecords([$activeAttendee, $archivedAttendee]);
});

test('invite action dispatches attendee invitations for the owner event', function () {
    asSuperAdmin();
    Bus::fake();

    $event = Event::factory()->create();

    livewire(EventAttendeesRelationManager::class, ['ownerRecord' => $event, 'pageClass' => ViewEvent::class])
        ->callAction(TestAction::make('invite')->table(), ['attendees' => ['invitee@example.com']])
        ->assertNotified();

    Bus::assertDispatched(CreateEventAttendees::class, function (CreateEventAttendees $job) use ($event): bool {
        $dispatchedEvent = (new ReflectionProperty($job, 'event'))->getValue($job);

        return $dispatchedEvent instanceof Event && $dispatchedEvent->is($event);
    });
});

describe('authorization', function () use ($eventAttendeesRelationManagerTestUser) {
    it('shows the Invite action with the `event_attendee.create` permission', function () use ($eventAttendeesRelationManagerTestUser) {
        $user = $eventAttendeesRelationManagerTestUser();
        $user->givePermissionTo(['event_attendee.view-any', 'event_attendee.create']);
        actingAs($user);

        $event = Event::factory()->create();

        livewire(EventAttendeesRelationManager::class, ['ownerRecord' => $event, 'pageClass' => ViewEvent::class])
            ->assertActionVisible(TestAction::make('invite')->table());
    });

    it('hides the Invite action without the `event_attendee.create` permission', function () use ($eventAttendeesRelationManagerTestUser) {
        $user = $eventAttendeesRelationManagerTestUser();
        $user->givePermissionTo('event_attendee.view-any');
        actingAs($user);

        $event = Event::factory()->create();

        livewire(EventAttendeesRelationManager::class, ['ownerRecord' => $event, 'pageClass' => ViewEvent::class])
            ->assertActionHidden(TestAction::make('invite')->table());
    });

    it('denies direct access without the `event_attendee.view-any` permission', function () use ($eventAttendeesRelationManagerTestUser) {
        $user = $eventAttendeesRelationManagerTestUser();
        actingAs($user);

        $event = Event::factory()->create();

        livewire(EventAttendeesRelationManager::class, ['ownerRecord' => $event, 'pageClass' => ViewEvent::class])
            ->assertForbidden();
    });
});

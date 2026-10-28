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

use AdvisingApp\MeetingCenter\Actions\CreateEventRegistrationFormVersion;
use AdvisingApp\MeetingCenter\Enums\EventAttendeeStatus;
use AdvisingApp\MeetingCenter\Models\Event;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use AdvisingApp\MeetingCenter\Models\EventRegistrationForm;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormSubmission;

$createRequestedSubmission = function (EventAttendee $attendee, EventRegistrationForm $form, array $attributes = []): void {
    EventRegistrationFormSubmission::factory()->create([
        'form_id' => $form->getKey(),
        'event_attendee_id' => $attendee->getKey(),
        'attendee_status' => EventAttendeeStatus::Invited,
        'submitted_at' => null,
        'canceled_at' => null,
        ...$attributes,
    ]);
};

it('has a pending invitation when the attendee is still invited', function (): void {
    $event = Event::factory()->create();
    $attendee = EventAttendee::factory()->create(['event_id' => $event->getKey(), 'status' => EventAttendeeStatus::Invited]);

    expect($attendee->hasPendingInvitation())->toBeTrue();
});

it('has a pending invitation when a registration form request is pending', function () use ($createRequestedSubmission): void {
    $event = Event::factory()->create();
    $attendee = EventAttendee::factory()->create(['event_id' => $event->getKey(), 'status' => EventAttendeeStatus::Attending]);

    expect($attendee->hasPendingInvitation())->toBeFalse();

    $createRequestedSubmission($attendee, $event->eventRegistrationForm);

    expect($attendee->hasPendingInvitation())->toBeTrue();
});

it('has a pending invitation when the request was sent before the registration form was edited', function () use ($createRequestedSubmission): void {
    $event = Event::factory()->create();
    $attendee = EventAttendee::factory()->create(['event_id' => $event->getKey(), 'status' => EventAttendeeStatus::Attending]);

    $createRequestedSubmission($attendee, $event->eventRegistrationForm);

    app(CreateEventRegistrationFormVersion::class)->execute($event->eventRegistrationForm, []);

    expect($attendee->hasPendingInvitation())->toBeTrue();
});

it('does not have a pending invitation when the attendee was not invited', function (EventAttendeeStatus $status): void {
    $event = Event::factory()->create();
    $attendee = EventAttendee::factory()->create(['event_id' => $event->getKey(), 'status' => $status]);

    expect($attendee->hasPendingInvitation())->toBeFalse();
})->with([
    'registering through the public link' => EventAttendeeStatus::Pending,
    'already answered yes' => EventAttendeeStatus::Attending,
    'already answered no' => EventAttendeeStatus::NotAttending,
]);

it('does not have a pending invitation when the registration form request is no longer pending', function (array $attributes) use ($createRequestedSubmission): void {
    $event = Event::factory()->create();
    $attendee = EventAttendee::factory()->create(['event_id' => $event->getKey(), 'status' => EventAttendeeStatus::Attending]);

    $createRequestedSubmission($attendee, $event->eventRegistrationForm, $attributes);

    expect($attendee->hasPendingInvitation())->toBeFalse();
})->with([
    'submitted' => fn (): array => ['submitted_at' => now()],
    'canceled' => fn (): array => ['canceled_at' => now()],
]);

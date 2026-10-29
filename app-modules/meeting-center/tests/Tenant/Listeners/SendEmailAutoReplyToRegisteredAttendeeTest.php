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

use AdvisingApp\MeetingCenter\Enums\EmailAutoReplyDeliveryPreference;
use AdvisingApp\MeetingCenter\Enums\EventAttendeeStatus;
use AdvisingApp\MeetingCenter\Jobs\SendEventRegistrationFormEmailAutoReply;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormSubmission;
use App\Features\AdmissionsAndEventsEmailAutoReplyFeature;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

$submit = fn (EventAttendee $attendee, EventAttendeeStatus $attendeeStatus): EventRegistrationFormSubmission => EventRegistrationFormSubmission::factory()->create([
    'form_id' => $attendee->event->eventRegistrationForm->getKey(),
    'event_attendee_id' => $attendee->getKey(),
    'attendee_status' => $attendeeStatus,
    'submitted_at' => now(),
]);

it('queues the `SendEventRegistrationFormEmailAutoReply` job when an attendee registers as attending', function (EventAttendeeStatus $status, ?EmailAutoReplyDeliveryPreference $requiredDeliveryPreference) use ($submit): void {
    $attendee = EventAttendee::factory()->create(['status' => $status]);

    Queue::fake([SendEventRegistrationFormEmailAutoReply::class]);

    $submit($attendee, EventAttendeeStatus::Attending);

    Queue::assertPushed(
        SendEventRegistrationFormEmailAutoReply::class,
        fn (SendEventRegistrationFormEmailAutoReply $job): bool => $job->attendee->is($attendee) && ($job->requiredDeliveryPreference === $requiredDeliveryPreference),
    );
})->with([
    'invited attendee' => [EventAttendeeStatus::Invited, EmailAutoReplyDeliveryPreference::AfterAttendanceConfirmation],
    'attendee registering on their own' => [EventAttendeeStatus::Pending, null],
    'attendee who declined before' => [EventAttendeeStatus::NotAttending, EmailAutoReplyDeliveryPreference::AfterAttendanceConfirmation],
]);

it('only queues the `SendEventRegistrationFormEmailAutoReply` job after attendance confirmation for a requested submission', function (EventAttendeeStatus $status): void {
    $attendee = EventAttendee::factory()->create(['status' => $status]);

    $submission = EventRegistrationFormSubmission::factory()->create([
        'form_id' => $attendee->event->eventRegistrationForm->getKey(),
        'event_attendee_id' => $attendee->getKey(),
        'attendee_status' => EventAttendeeStatus::Invited,
        'submitted_at' => null,
        'requester_id' => User::factory()->create()->getKey(),
    ]);

    Queue::fake([SendEventRegistrationFormEmailAutoReply::class]);

    $submission->update(['submitted_at' => now(), 'attendee_status' => EventAttendeeStatus::Attending]);

    Queue::assertPushed(
        SendEventRegistrationFormEmailAutoReply::class,
        fn (SendEventRegistrationFormEmailAutoReply $job): bool => $job->requiredDeliveryPreference === EmailAutoReplyDeliveryPreference::AfterAttendanceConfirmation,
    );
})->with([
    'attendee registering for the first time' => [EventAttendeeStatus::Pending],
    'attendee who is already attending and was invited again' => [EventAttendeeStatus::Attending],
]);

it('does not queue the `SendEventRegistrationFormEmailAutoReply` job when an attendee who is already attending confirms again', function () use ($submit): void {
    $attendee = EventAttendee::factory()->create(['status' => EventAttendeeStatus::Attending]);

    Queue::fake([SendEventRegistrationFormEmailAutoReply::class]);

    $submit($attendee, EventAttendeeStatus::Attending);

    Queue::assertNotPushed(SendEventRegistrationFormEmailAutoReply::class);
});

it('does not queue the `SendEventRegistrationFormEmailAutoReply` job when an attendee registers as not attending', function () use ($submit): void {
    $attendee = EventAttendee::factory()->create(['status' => EventAttendeeStatus::Invited]);

    Queue::fake([SendEventRegistrationFormEmailAutoReply::class]);

    $submit($attendee, EventAttendeeStatus::NotAttending);

    Queue::assertNotPushed(SendEventRegistrationFormEmailAutoReply::class);
});

it('does not queue the `SendEventRegistrationFormEmailAutoReply` job when the feature is inactive', function () use ($submit): void {
    $attendee = EventAttendee::factory()->create(['status' => EventAttendeeStatus::Pending]);

    AdmissionsAndEventsEmailAutoReplyFeature::deactivate();

    Queue::fake([SendEventRegistrationFormEmailAutoReply::class]);

    $submit($attendee, EventAttendeeStatus::Attending);

    Queue::assertNotPushed(SendEventRegistrationFormEmailAutoReply::class);
});

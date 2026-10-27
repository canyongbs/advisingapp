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
use AdvisingApp\MeetingCenter\Models\Event;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use AdvisingApp\MeetingCenter\Notifications\EventRegistrationFormEmailAutoReplyNotification;
use Illuminate\Support\Facades\Notification;

$createAttendee = function (bool $isEnabled, EmailAutoReplyDeliveryPreference $deliveryPreference): EventAttendee {
    $event = Event::factory()->create();

    $event->eventRegistrationForm->emailAutoReply()->firstOrFail()->update([
        'is_enabled' => $isEnabled,
        'delivery_preference' => $deliveryPreference,
    ]);

    return EventAttendee::factory()->create([
        'event_id' => $event->getKey(),
        'status' => EventAttendeeStatus::Invited,
    ]);
};

it('sends the email auto reply when it is enabled with the required delivery preference', function (EmailAutoReplyDeliveryPreference $deliveryPreference, ?EmailAutoReplyDeliveryPreference $requiredDeliveryPreference) use ($createAttendee): void {
    $attendee = $createAttendee(true, $deliveryPreference);

    Notification::fake();

    (new SendEventRegistrationFormEmailAutoReply($attendee, $requiredDeliveryPreference))->handle();

    Notification::assertSentToTimes($attendee, EventRegistrationFormEmailAutoReplyNotification::class, 1);
})->with([
    'when invited' => [EmailAutoReplyDeliveryPreference::WhenInvited, EmailAutoReplyDeliveryPreference::WhenInvited],
    'after attendance confirmation' => [EmailAutoReplyDeliveryPreference::AfterAttendanceConfirmation, EmailAutoReplyDeliveryPreference::AfterAttendanceConfirmation],
    'when invited with any preference required' => [EmailAutoReplyDeliveryPreference::WhenInvited, null],
    'after attendance confirmation with any preference required' => [EmailAutoReplyDeliveryPreference::AfterAttendanceConfirmation, null],
]);

it('does not send the email auto reply when it has a different delivery preference', function (EmailAutoReplyDeliveryPreference $deliveryPreference, EmailAutoReplyDeliveryPreference $requiredDeliveryPreference) use ($createAttendee): void {
    $attendee = $createAttendee(true, $deliveryPreference);

    Notification::fake();

    (new SendEventRegistrationFormEmailAutoReply($attendee, $requiredDeliveryPreference))->handle();

    Notification::assertNotSentTo($attendee, EventRegistrationFormEmailAutoReplyNotification::class);
})->with([
    'when invited' => [EmailAutoReplyDeliveryPreference::WhenInvited, EmailAutoReplyDeliveryPreference::AfterAttendanceConfirmation],
    'after attendance confirmation' => [EmailAutoReplyDeliveryPreference::AfterAttendanceConfirmation, EmailAutoReplyDeliveryPreference::WhenInvited],
]);

it('does not send the email auto reply when it is disabled', function () use ($createAttendee): void {
    $attendee = $createAttendee(false, EmailAutoReplyDeliveryPreference::WhenInvited);

    Notification::fake();

    (new SendEventRegistrationFormEmailAutoReply($attendee))->handle();

    Notification::assertNotSentTo($attendee, EventRegistrationFormEmailAutoReplyNotification::class);
});

it('does not send the email auto reply when invited to an attendee who declined before it was sent', function () use ($createAttendee): void {
    $attendee = $createAttendee(true, EmailAutoReplyDeliveryPreference::WhenInvited);
    $attendee->status = EventAttendeeStatus::NotAttending;
    $attendee->save();

    Notification::fake();

    (new SendEventRegistrationFormEmailAutoReply($attendee, EmailAutoReplyDeliveryPreference::WhenInvited))->handle();

    Notification::assertNotSentTo($attendee, EventRegistrationFormEmailAutoReplyNotification::class);
});

it('does not send the email auto reply when the event was archived before it was sent', function () use ($createAttendee): void {
    $attendee = $createAttendee(true, EmailAutoReplyDeliveryPreference::WhenInvited);
    $attendee->event->archive();

    Notification::fake();

    (new SendEventRegistrationFormEmailAutoReply($attendee, EmailAutoReplyDeliveryPreference::WhenInvited))->handle();

    Notification::assertNotSentTo($attendee, EventRegistrationFormEmailAutoReplyNotification::class);
});

it('does not send the email auto reply when the attendee was archived before it was sent', function () use ($createAttendee): void {
    $attendee = $createAttendee(true, EmailAutoReplyDeliveryPreference::WhenInvited);
    $attendee->archive();

    Notification::fake();

    (new SendEventRegistrationFormEmailAutoReply($attendee, EmailAutoReplyDeliveryPreference::WhenInvited))->handle();

    Notification::assertNotSentTo($attendee, EventRegistrationFormEmailAutoReplyNotification::class);
});

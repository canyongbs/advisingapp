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
use AdvisingApp\MeetingCenter\Jobs\CreateEventAttendee;
use AdvisingApp\MeetingCenter\Jobs\SendEventRegistrationFormEmailAutoReply;
use AdvisingApp\MeetingCenter\Models\Event;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;

beforeEach(function (): void {
    Notification::fake();
});

$isEmailAutoReplyJobFor = fn (EventAttendee $attendee): Closure => fn (SendEventRegistrationFormEmailAutoReply $job): bool => $job->attendee->is($attendee)
    && ($job->requiredDeliveryPreference === EmailAutoReplyDeliveryPreference::WhenInvited);

it('queues the email auto reply for the invited attendee', function () use ($isEmailAutoReplyJobFor): void {
    $event = Event::factory()->create();

    Queue::fake([SendEventRegistrationFormEmailAutoReply::class]);

    (new CreateEventAttendee($event, 'jamie@example.com', User::factory()->create()))->handle();

    $attendee = $event->attendees()->where('email', 'jamie@example.com')->sole();

    Queue::assertPushed(SendEventRegistrationFormEmailAutoReply::class, $isEmailAutoReplyJobFor($attendee));
});

it('queues the email auto reply again when an archived attendee is invited again', function () use ($isEmailAutoReplyJobFor): void {
    $event = Event::factory()->create();

    $attendee = EventAttendee::factory()->create([
        'event_id' => $event->getKey(),
        'email' => 'jamie@example.com',
        'status' => EventAttendeeStatus::Invited,
    ]);
    $attendee->archive();

    Queue::fake([SendEventRegistrationFormEmailAutoReply::class]);

    (new CreateEventAttendee($event, 'jamie@example.com', User::factory()->create()))->handle();

    Queue::assertPushed(SendEventRegistrationFormEmailAutoReply::class, $isEmailAutoReplyJobFor($attendee));
});

it('does not queue the email auto reply when the attendee was already invited', function (): void {
    $event = Event::factory()->create();

    EventAttendee::factory()->create([
        'event_id' => $event->getKey(),
        'email' => 'jamie@example.com',
        'status' => EventAttendeeStatus::Invited,
    ]);

    Queue::fake([SendEventRegistrationFormEmailAutoReply::class]);

    (new CreateEventAttendee($event, 'jamie@example.com', User::factory()->create()))->handle();

    Queue::assertNotPushed(SendEventRegistrationFormEmailAutoReply::class);
});

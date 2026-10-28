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

namespace AdvisingApp\MeetingCenter\Listeners;

use AdvisingApp\MeetingCenter\Enums\EmailAutoReplyDeliveryPreference;
use AdvisingApp\MeetingCenter\Enums\EventAttendeeStatus;
use AdvisingApp\MeetingCenter\Events\EventRegistrationFormSubmissionCreated;
use AdvisingApp\MeetingCenter\Jobs\SendEventRegistrationFormEmailAutoReply;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use App\Features\AdmissionsAndEventsEmailAutoReplyFeature;

class SendEmailAutoReplyToRegisteredAttendee
{
    /**
     * Only attendees who change their response to attending receive the email auto reply, so
     * confirming again does not send it twice, unless they were invited again with a new request.
     * Attendees registering through the public link for the first time were never invited, so
     * they receive it now whichever delivery preference is chosen. Anyone else was invited or
     * has responded before, and already received it with their invitation unless it is sent
     * after attendance confirmation.
     */
    public function handle(EventRegistrationFormSubmissionCreated $event): void
    {
        if (! AdmissionsAndEventsEmailAutoReplyFeature::active()) {
            return;
        }

        $submission = $event->submission;

        if ($submission->attendee_status !== EventAttendeeStatus::Attending) {
            return;
        }

        if (($event->previousAttendeeStatus === EventAttendeeStatus::Attending) && blank($submission->requester_id)) {
            return;
        }

        $attendee = $submission->getRelationValue('author');

        if (! $attendee instanceof EventAttendee) {
            return;
        }

        $isFirstRegistration = ($event->previousAttendeeStatus === EventAttendeeStatus::Pending) && blank($submission->requester_id);

        dispatch(new SendEventRegistrationFormEmailAutoReply(
            attendee: $attendee,
            requiredDeliveryPreference: $isFirstRegistration ? null : EmailAutoReplyDeliveryPreference::AfterAttendanceConfirmation,
        ))->afterCommit();
    }
}

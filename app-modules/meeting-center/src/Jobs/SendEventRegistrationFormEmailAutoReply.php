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

namespace AdvisingApp\MeetingCenter\Jobs;

use AdvisingApp\MeetingCenter\Enums\EmailAutoReplyDeliveryPreference;
use AdvisingApp\MeetingCenter\Enums\EventAttendeeStatus;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use AdvisingApp\MeetingCenter\Notifications\EventRegistrationFormEmailAutoReplyNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendEventRegistrationFormEmailAutoReply implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @param ?EmailAutoReplyDeliveryPreference $requiredDeliveryPreference
     */
    public function __construct(
        public EventAttendee $attendee,
        public ?EmailAutoReplyDeliveryPreference $requiredDeliveryPreference = null,
    ) {}

    public function handle(): void
    {
        if ($this->attendee->isArchived() || $this->attendee->event->isArchived()) {
            return;
        }

        if (
            ($this->requiredDeliveryPreference === EmailAutoReplyDeliveryPreference::WhenInvited)
            && ($this->attendee->status === EventAttendeeStatus::NotAttending)
        ) {
            return;
        }

        $emailAutoReply = $this->attendee->event->eventRegistrationForm?->emailAutoReply;

        if (! $emailAutoReply?->is_enabled) {
            return;
        }

        if ($this->requiredDeliveryPreference instanceof EmailAutoReplyDeliveryPreference && ($emailAutoReply->delivery_preference !== $this->requiredDeliveryPreference)) {
            return;
        }

        if (! $this->attendee->canReceiveEmail()) {
            return;
        }

        $this->attendee->notify(new EventRegistrationFormEmailAutoReplyNotification($this->attendee));
    }
}

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

namespace AdvisingApp\InAppCommunication\Jobs;

use AdvisingApp\InAppCommunication\Actions\CheckConversationMessageContentForMention;
use AdvisingApp\InAppCommunication\Actions\ConvertMessageJsonToText;
use AdvisingApp\InAppCommunication\Enums\ConversationNotificationPreference;
use AdvisingApp\InAppCommunication\Events\ConversationMessageSent;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class NotifyConversationParticipant implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        readonly public ConversationMessageSent $event,
        readonly public User $participant,
    ) {}

    public function handle(
        CheckConversationMessageContentForMention $checkConversationMessageContentForMention,
        ConvertMessageJsonToText $convertMessageJsonToText,
    ): void {
        $participation = $this->event->conversation->participants()->find($this->participant)?->participant;

        if (! $participation) {
            return;
        }

        if ($participation->notification_preference === ConversationNotificationPreference::None) {
            return;
        }

        if (
            ($participation->notification_preference === ConversationNotificationPreference::Mentions) &&
            (! $checkConversationMessageContentForMention($this->event->messageContent, $this->participant))
        ) {
            return;
        }

        $participation->first_unread_message_sid ??= $this->event->messageSid;
        $participation->first_unread_message_at ??= $this->event->messageSentAt;
        $participation->last_unread_message_content = $convertMessageJsonToText($this->event->messageContent);
        $participation->increment('unread_messages_count');
        $participation->touch('updated_at');
        $participation->save();
    }
}

<?php

namespace AdvisingApp\InAppCommunication\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use AdvisingApp\InAppCommunication\Events\ConversationMessageSent;
use AdvisingApp\InAppCommunication\Actions\ConvertMessageJsonToText;
use AdvisingApp\InAppCommunication\Enums\ConversationNotificationPreference;
use AdvisingApp\InAppCommunication\Actions\CheckConversationMessageContentForMention;

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

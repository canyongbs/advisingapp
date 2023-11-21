<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Assistant\Notifications;

use App\Models\User;
use App\Models\NotificationSetting;
use Illuminate\Bus\Queueable;
use App\Notifications\MailMessage;
use Assist\Assistant\Models\AssistantChat;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Assist\Assistant\Services\AIInterface\Enums\AIChatMessageFrom;

class SendAssistantTranscriptNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected AssistantChat $chat,
        protected User $sender
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        $message = MailMessage::make()
            ->emailTemplate($this->resolveEmailTemplate())
            ->greeting("Hello {$notifiable->name},");

        $senderIsNotifiable = $this->sender->is($notifiable);

        if ($senderIsNotifiable) {
            $message->subject("Assistant Chat Transcript: {$this->chat->name}")
                ->line('Here is a copy of your chat with Canyon:');
        } else {
            $message->subject("An Assistant Chat Transcript has been shared with you: {$this->chat->name}")
                ->line("Here is a copy of {$this->sender->name}'s chat with Canyon:");
        }

        $this->chat
            ->messages
            ->each(function ($chatMessage) use ($senderIsNotifiable, $message) {
                if ($chatMessage->from === AIChatMessageFrom::User) {
                    if ($senderIsNotifiable) {
                        $message->line("You: {$chatMessage->message}");
                    } else {
                        $message->line("{$this->sender->name}: {$chatMessage->message}");
                    }
                } else {
                    $message->line("Canyon: {$chatMessage->message}");
                }
            });

        return $message;
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }

    private function resolveEmailTemplate(): ?NotificationSetting
    {
        return $this->sender->teams()->first()?->division?->emailTemplate;
    }
}

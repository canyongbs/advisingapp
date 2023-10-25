<?php

namespace Assist\Assistant\Notifications;

use Illuminate\Bus\Queueable;
use Assist\Assistant\Models\AssistantChat;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Assist\Assistant\Services\AIInterface\Enums\AIChatMessageFrom;

class SendAssistantTranscriptNotification extends Notification
{
    use Queueable;

    public function __construct(protected AssistantChat $chat) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage())
            ->subject("Assistant Chat Transcript: {$this->chat->name}")
            ->greeting("Hello {$notifiable->name},")
            ->line('Here is a copy of your chat with Canyon:');

        $this->chat->messages->each(function ($chatMessage) use ($message) {
            if ($chatMessage->from === AIChatMessageFrom::User) {
                $message->line("You: {$chatMessage->message}");
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
        return [
        ];
    }
}

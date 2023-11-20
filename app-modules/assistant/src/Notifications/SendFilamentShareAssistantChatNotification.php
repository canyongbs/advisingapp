<?php

namespace Assist\Assistant\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Assist\Assistant\Enums\AssistantChatShareVia;
use Filament\Notifications\Notification as FilamentNotification;

class SendFilamentShareAssistantChatNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected AssistantChatShareVia $via,
        protected string $name,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(User $notifiable): array
    {
        return match ($this->via) {
            AssistantChatShareVia::Email => FilamentNotification::make()
                ->success()
                ->title("You emailed an assistant chat to team {$this->name}.")
                ->getDatabaseMessage(),
            AssistantChatShareVia::Internal => FilamentNotification::make()
                ->success()
                ->title("You shared an assistant chat with team {$this->name}.")
                ->getDatabaseMessage(),
        };
    }
}

<?php

namespace Assist\Assistant\Jobs;

use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Filament\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Assist\Assistant\Models\AssistantChat;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Assist\Assistant\Enums\AssistantChatShareVia;
use Assist\Assistant\Enums\AssistantChatShareWith;

class ShareAssistantChatNotificationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use Batchable;

    public function __construct(
        protected AssistantChat $chat,
        protected AssistantChatShareVia $via,
        protected AssistantChatShareWith $targetType,
        protected string $name,
        protected User $sender
    ) {}

    public function handle(): void
    {
        if ($this->batch()?->cancelled()) {
            return;
        }

        $notification = Notification::make()
            ->success();

        switch ($this->via) {
            case AssistantChatShareVia::Email:
                switch ($this->targetType) {
                    case AssistantChatShareWith::User:
                        $notification->title("You emailed an assistant chat to {$this->name}.");

                        break;
                    case AssistantChatShareWith::Team:
                        $notification->title("You emailed an assistant chat to team {$this->name}.");

                        break;
                }

                break;
            case AssistantChatShareVia::Internal:
                switch ($this->targetType) {
                    case AssistantChatShareWith::User:
                        $notification->title("You shared an assistant chat with {$this->name}.");

                        break;
                    case AssistantChatShareWith::Team:
                        $notification->title("You shared an assistant chat with team {$this->name}.");

                        break;
                }

                break;
        }

        $notification->sendToDatabase($this->sender);
    }
}

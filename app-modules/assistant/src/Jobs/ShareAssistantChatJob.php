<?php

namespace Assist\Assistant\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Filament\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Assist\Assistant\Models\AssistantChat;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Assist\Assistant\Enums\AssistantChatShareVia;
use Assist\Assistant\Models\AssistantChatMessage;
use Assist\Assistant\Notifications\SendAssistantTranscriptNotification;

class ShareAssistantChatJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected AssistantChat $chat,
        protected AssistantChatShareVia $via,
        protected User $user,
        protected User $sender
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        switch ($this->via) {
            case AssistantChatShareVia::Email:
                $this->user->notify(new SendAssistantTranscriptNotification($this->chat, $this->sender));

                Notification::make()
                    ->success()
                    ->title("You emailed an assistant chat to {$this->user->name}.")
                    ->sendToDatabase($this->sender);

                break;
            case AssistantChatShareVia::Internal:
                $replica = $this->chat
                    ->replicate(['id', 'user_id'])
                    ->user()
                    ->associate($this->user);

                $replica->save();

                $this->chat
                    ->messages()
                    ->each(
                        fn (AssistantChatMessage $message) => $message
                            ->replicate(['id', 'assistant_chat_id'])
                            ->chat()
                            ->associate($replica)
                            ->save()
                    );

                Notification::make()
                    ->success()
                    ->title("You shared an assistant chat with {$this->user->name}.")
                    ->sendToDatabase($this->sender);

                break;
        }
    }
}

<?php

namespace Assist\Assistant\Jobs;

use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
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
    use Batchable;

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
        if ($this->batch()?->cancelled()) {
            return;
        }

        switch ($this->via) {
            case AssistantChatShareVia::Email:
                $this->user->notify(new SendAssistantTranscriptNotification($this->chat, $this->sender));

                break;
            case AssistantChatShareVia::Internal:

                $replica = $this->chat
                    ->replicate(['id', 'user_id', 'assistant_chat_folder_id'])
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

                break;
        }
    }
}

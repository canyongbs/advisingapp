<?php

namespace Assist\Assistant\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Assist\Assistant\Models\AssistantChat;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Assist\Assistant\Enums\AssistantChatShareVia;

class ShareAssistantChatsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected AssistantChat $chat,
        protected AssistantChatShareVia $via,
        protected Collection $users,
        protected User $sender
    ) {}

    public function handle(): void
    {
        //TODO: batching
        //TODO: notification cleanup, i.e. teams->name
        $this->users->each(fn (User $user) => dispatch(new ShareAssistantChatJob($this->chat, $this->via, $user, $this->sender)));
    }
}

<?php

namespace Assist\Assistant\Jobs;

use App\Models\User;
use Assist\Team\Models\Team;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Queue\SerializesModels;
use Filament\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Assist\Assistant\Models\AssistantChat;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Assist\Assistant\Enums\AssistantChatShareVia;
use Assist\Assistant\Enums\AssistantChatShareWith;

class ShareAssistantChatsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected AssistantChat $chat,
        protected AssistantChatShareVia $via,
        protected AssistantChatShareWith $targetType,
        protected array $targetIds,
        protected User $sender
    ) {}

    public function handle(): void
    {
        if ($this->targetType === AssistantChatShareWith::User) {
            User::whereIn('id', $this->targetIds)
                ->get()
                ->each(function (User $user) {
                    dispatch(new ShareAssistantChatJob($this->chat, $this->via, $user, $this->sender));
                    dispatch(new ShareAssistantChatNotificationJob($this->chat, $this->via, $this->targetType, $user->name, $this->sender));
                });
        } elseif ($this->targetType === AssistantChatShareWith::Team) {
            Team::whereIn('id', $this->targetIds)
                ->with('users')
                ->get()
                ->each(function (Team $team) {
                    $jobs = $team
                        ->users
                        ->map(fn (User $user) => new ShareAssistantChatJob($this->chat, $this->via, $user, $this->sender));

                    $jobs[] = new ShareAssistantChatNotificationJob($this->chat, $this->via, $this->targetType, $team->name, $this->sender);

                    Bus::batch($jobs)
                        ->name("ShareAssistantChatJobs with Team: {$team->id}")
                        ->dispatch();
                });
        }
    }
}

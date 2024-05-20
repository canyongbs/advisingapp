<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Ai\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use AdvisingApp\Team\Models\Team;
use Illuminate\Support\Facades\Bus;
use Illuminate\Queue\SerializesModels;
use Filament\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use AdvisingApp\Ai\Enums\AssistantChatShareVia;
use AdvisingApp\Assistant\Models\AssistantChat;
use AdvisingApp\Ai\Enums\AssistantChatShareWith;
use AdvisingApp\Ai\Notifications\SendFilamentShareAssistantChatNotification;

class ShareAssistantChatsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public AssistantChat $chat,
        public AssistantChatShareVia $via,
        public AssistantChatShareWith $targetType,
        public array $targetIds,
        public User $sender
    ) {}

    public function handle(): void
    {
        if ($this->targetType === AssistantChatShareWith::User) {
            User::query()
                ->whereKey($this->targetIds)
                ->get()
                ->each(function (User $user) {
                    dispatch(new ShareAssistantChatJob($this->chat, $this->via, $user, $this->sender));

                    switch ($this->via) {
                        case AssistantChatShareVia::Email:
                            $name = $this->sender->is($user) ? 'yourself' : $user->name;
                            Notification::make()
                                ->success()
                                ->title("You emailed an assistant chat to {$name}.")
                                ->sendToDatabase($this->sender);

                            break;
                        case AssistantChatShareVia::Internal:
                            Notification::make()
                                ->success()
                                ->title("You shared an assistant chat with {$user->name}.")
                                ->sendToDatabase($this->sender);

                            break;
                    }
                });

            return;
        }

        if ($this->targetType === AssistantChatShareWith::Team) {
            $sender = $this->sender;
            $via = $this->via;

            Team::query()
                ->whereKey($this->targetIds)
                ->with('users')
                ->get()
                ->each(function (Team $team) use ($sender, $via) {
                    $jobs = $team
                        ->users()
                        ->whereKeyNot($sender->id)
                        ->get()
                        ->map(fn (User $user) => new ShareAssistantChatJob($this->chat, $this->via, $user, $this->sender));

                    Bus::batch($jobs)
                        ->name("ShareAssistantChatJobs with Team: {$team->id}")
                        ->then(fn () => $sender->notify(new SendFilamentShareAssistantChatNotification($via, $team->name)))
                        ->dispatch();
                });
        }
    }
}

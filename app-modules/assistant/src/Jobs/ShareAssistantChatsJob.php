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
use Assist\Assistant\Notifications\SendFilamentShareAssistantChatNotification;

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
        } elseif ($this->targetType === AssistantChatShareWith::Team) {
            $sender = $this->sender;
            $via = $this->via;

            Team::whereIn('id', $this->targetIds)
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

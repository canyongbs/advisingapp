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
    - Test

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Assistant\Jobs;

use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use AdvisingApp\Assistant\Models\AssistantChat;
use AdvisingApp\Assistant\Enums\AssistantChatShareVia;
use AdvisingApp\Assistant\Models\AssistantChatMessage;
use AdvisingApp\Assistant\Notifications\SendAssistantTranscriptNotification;

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

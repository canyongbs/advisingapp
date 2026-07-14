<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Ai\Jobs\Advisors;

use AdvisingApp\Ai\Enums\AiThreadShareTarget;
use AdvisingApp\Ai\Models\AiThread;
use AdvisingApp\Team\Models\Department;
use App\Models\User;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;

class PrepareAiThreadEmailing implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public AiThread $thread,
        public string $targetType,
        public array $targetIds,
        public User $sender,
    ) {}

    public function handle(): void
    {
        if ($this->targetType === AiThreadShareTarget::User->value) {
            User::query()
                ->whereKey($this->targetIds)
                ->get()
                ->each(function (User $recipient) {
                    dispatch(new EmailAiThread($this->thread, $this->sender, $recipient));

                    $recipientName = $this->sender->is($recipient) ? 'yourself' : $recipient->name;

                    FilamentNotification::make()
                        ->success()
                        ->title("You emailed an AI chat to {$recipientName}.")
                        ->sendToDatabase($this->sender);
                });

            return;
        }

        if ($this->targetType === AiThreadShareTarget::Department->value) {
            $sender = $this->sender;

            Department::query()
                ->whereKey($this->targetIds)
                ->with('users')
                ->get()
                ->each(function (Department $department) use ($sender) {
                    Bus::batch(
                        $department->users()->whereKeyNot($this->sender)->get()
                            ->map(fn (User $recipient) => new EmailAiThread($this->thread, $this->sender, $recipient))
                            ->all(),
                    )
                        ->name("PrepareAiThreadEmailing for department {$department->id}")
                        ->then(function () use ($sender, $department) {
                            FilamentNotification::make()
                                ->success()
                                ->title("You emailed an AI chat to users in department {$department->name}.")
                                ->sendToDatabase($sender);
                        })
                        ->dispatch();
                });
        }
    }
}

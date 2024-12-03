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
use Illuminate\Bus\Batch;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use AdvisingApp\Team\Models\Team;
use AdvisingApp\Ai\Models\AiThread;
use Illuminate\Support\Facades\Bus;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use AdvisingApp\Ai\Enums\AiThreadShareTarget;
use Filament\Notifications\Notification as FilamentNotification;

class PrepareAiThreadCloning implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public AiThread $thread,
        public AiThreadShareTarget $targetType,
        public array $targetIds,
        public User $sender,
    ) {}

    public function handle(): void
    {
        $threadName = $this->thread->name;

        $sender = $this->sender;

        Bus::batch(
            match ($this->targetType) {
                AiThreadShareTarget::User => $this->generateSingleUserShareJobs(),
                AiThreadShareTarget::Team => $this->generateTeamShareJobs(),
            },
        )
            ->name("AiThreadCloning batch for {$this->targetType->getLabel()}")
            ->finally(function (Batch $batch) use ($threadName, $sender) {
                $notification = FilamentNotification::make()
                    ->title("AI chat cloning for {$threadName} processing completed.");

                if ($batch->failedJobs > 0) {
                    if ($batch->failedJobs === $batch->totalJobs) {
                        $notification->error();

                        $notification->body('Failed to clone chat to any users.');
                    } else {
                        $notification->warning();

                        $successfulUsersCount = $batch->totalJobs - $batch->failedJobs;

                        $sucessfulUsersString = Str::plural('user', $successfulUsersCount);

                        $failedUsersString = Str::plural('user', $batch->failedJobs);

                        $notification->body(
                            <<<EOT
                            Successfully cloned chat to {$successfulUsersCount} {$sucessfulUsersString}.
                            Failed to clone chat to {$batch->failedJobs} {$failedUsersString}.
                            EOT
                        );
                    }
                } else {
                    $notification->success();

                    $usersString = Str::plural('user', $batch->totalJobs);

                    $notification->body("Successfully cloned chat to {$batch->totalJobs} {$usersString}.");
                }

                $notification->sendToDatabase($sender);
            })
            ->dispatch();
    }

    protected function generateSingleUserShareJobs(): array
    {
        return User::query()
            ->whereKey($this->targetIds)
            ->get()
            ->map(function (User $recipient) {
                return new CloneAiThread($this->thread, $this->sender, $recipient);
            })
            ->all();
    }

    protected function generateTeamShareJobs(): array
    {
        return Team::query()
            ->whereKey($this->targetIds)
            ->with('users')
            ->get()
            ->map(function (Team $team) {
                return $team->users()
                    ->whereKeyNot($this->sender)
                    ->get()
                    ->map(fn (User $recipient) => new CloneAiThread($this->thread, $this->sender, $recipient))
                    ->all();
            })
            ->toArray();
    }
}

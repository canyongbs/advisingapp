<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Research\Jobs;

use AdvisingApp\Research\Enums\ResearchRequestShareTarget;
use AdvisingApp\Research\Models\ResearchRequest;
use AdvisingApp\Team\Models\Team;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;

class PrepareResearchRequestEmailing implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /** @param array<string, mixed> $targetIds */
    public function __construct(
        public ResearchRequest $researchRequest,
        public string $targetType,
        public array $targetIds,
        public ?string $note,
        public User $sender,
    ) {}

    public function handle(): void
    {
        if ($this->targetType === ResearchRequestShareTarget::User->value) {
            User::query()
                ->whereKey($this->targetIds)
                ->get()
                ->each(function (User $recipient) {
                    dispatch(new EmailResearchRequest($this->researchRequest, $this->note, $this->sender, $recipient, true));
                });

            return;
        }

        if ($this->targetType === ResearchRequestShareTarget::Team->value) {
            $sender = $this->sender;

            Team::query()
                ->whereKey($this->targetIds)
                ->with('users')
                ->get()
                ->each(function (Team $team) use ($sender) {
                    Bus::batch(
                        $team->users()->whereKeyNot($this->sender)->get()
                            ->map(fn (User $recipient) => new EmailResearchRequest($this->researchRequest, $this->note, $this->sender, $recipient, false))
                            ->all(),
                    )
                        ->name("PrepareResearchReportEmailing for team {$team->getKey()}")
                        ->finally(function (Batch $batch) use ($sender, $team) {
                            Notification::make()
                                ->success()
                                ->title("You emailed a research report to {$batch->processedJobs()} users in team {$team->name}.")
                                ->sendToDatabase($sender);
                        })
                        ->dispatch();
                });
        }
    }
}

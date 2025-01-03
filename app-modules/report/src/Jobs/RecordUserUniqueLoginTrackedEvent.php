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

namespace AdvisingApp\Report\Jobs;

use AdvisingApp\Report\Enums\TrackedEventType;
use AdvisingApp\Report\Models\TrackedEventCount;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Throwable;

class RecordUserUniqueLoginTrackedEvent implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Carbon $occurredAt,
        public User $user,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            DB::beginTransaction();

            $this->user
                ->logins()
                ->create([
                    'type' => TrackedEventType::UserLogin,
                    'occurred_at' => $this->occurredAt,
                ]);

            DB::table('tracked_event_counts')
                ->upsert(
                    [
                        [
                            'id' => (new TrackedEventCount())->newUniqueId(),
                            'type' => TrackedEventType::UserLogin,
                            'count' => 1,
                            'last_occurred_at' => $this->occurredAt,
                            'updated_at' => now(),
                            'created_at' => now(),
                            'related_to_id' => $this->user->id,
                            'related_to_type' => $this->user->getMorphClass(),
                        ],
                    ],
                    ['related_to_type', 'related_to_id', 'type'],
                    [
                        'count' => DB::raw('tracked_event_counts.count + 1'),
                        'last_occurred_at' => DB::raw("GREATEST(tracked_event_counts.last_occurred_at, '{$this->occurredAt}')"),
                        'updated_at' => now(),
                    ]
                );

            $this->user->update([
                'first_login_at' => $this->user->first_login_at ?? $this->occurredAt,
                'last_logged_in_at' => $this->occurredAt,
            ]);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            throw $e;
        }
    }
}

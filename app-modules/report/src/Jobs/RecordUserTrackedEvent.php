<?php

namespace AdvisingApp\Report\Jobs;

use AdvisingApp\Report\Enums\TrackedEventType;
use AdvisingApp\Report\Models\TrackedEvent;
use AdvisingApp\Report\Models\TrackedEventCount;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use App\Models\Authenticatable;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Throwable;

class RecordUserTrackedEvent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public TrackedEventType $type,
        public Carbon $occurredAt,
        public User $user,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            \Log::debug('handle');
            DB::beginTransaction();

            TrackedEvent::create([
                'type' => $this->type,
                'occurred_at' => $this->occurredAt,
                'related_to_id' => $this->user->id,
                'related_to_type' => $this->user,
            ]);

            DB::table('tracked_event_counts')
                ->upsert(
                    [
                        [
                            'id' => (new TrackedEventCount())->newUniqueId(),
                            'type' => $this->type,
                            'count' => 1,
                            'last_occurred_at' => $this->occurredAt,
                            'updated_at' => now(),
                            'created_at' => now(),
                            'related_to_id' => $this->user->id,
                            'related_to_type' => $this->user,
                        ],
                    ],
                    ['type', 'related_to_id', 'related_to_type'],
                    [
                        'count' => DB::raw('tracked_event_counts.count + 1'),
                        'last_occurred_at' => DB::raw("GREATEST(tracked_event_counts.last_occurred_at, '{$this->occurredAt}')"),
                        'updated_at' => now(),
                    ]
                );

            $this->user
                ->update([
                    'first_login_at' => $this->user->first_login_at ?? now(),
                    'last_logged_in_at' => now()
                ]);

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            throw $e;
        }
    }
}

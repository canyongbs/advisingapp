<?php

namespace AdvisingApp\Report\Jobs;

use Throwable;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use AdvisingApp\Report\Models\TrackedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use AdvisingApp\Report\Enums\TrackedEventType;
use AdvisingApp\Report\Models\TrackedEventCount;

class RecordTrackedEvent implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public TrackedEventType $type,
        public Carbon $occurredAt,
    ) {}

    public function handle(): void
    {
        try {
            DB::beginTransaction();

            TrackedEvent::create([
                'type' => $this->type,
                'occurred_at' => $this->occurredAt,
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
                        ],
                    ],
                    ['type'],
                    [
                        'count' => DB::raw('tracked_event_counts.count + 1'),
                        'last_occurred_at' => DB::raw("GREATEST(tracked_event_counts.last_occurred_at, '{$this->occurredAt}')"),
                        'updated_at' => now(),
                    ]
                );

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            throw $e;
        }
    }
}

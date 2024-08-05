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
                    values: [
                        'type' => $this->type,
                        'count' => DB::raw('count + 1'),
                        'last_occurred_at' => DB::raw('GREATEST(last_occurred_at, ?)', [$this->occurredAt]),
                    ],
                    uniqueBy: ['type'],
                    update: ['count', 'last_occurred_at']
                );

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();
        }
    }
}

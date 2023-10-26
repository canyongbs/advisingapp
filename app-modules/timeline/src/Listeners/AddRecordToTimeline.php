<?php

namespace Assist\Timeline\Listeners;

use Assist\Timeline\Models\Timeline;
use Assist\Timeline\Events\TimelineableRecordCreated;

class AddRecordToTimeline
{
    public function handle(TimelineableRecordCreated $event): void
    {
        /** @var Educatable $educatable */
        $educatable = $event->educatable;

        cache()->forget("timeline.synced.{$educatable->getMorphClass()}.{$educatable->getKey()}");

        Timeline::firstOrCreate([
            'educatable_type' => $educatable->getMorphClass(),
            'educatable_id' => $educatable->getKey(),
            'timelineable_type' => $event->model->getMorphClass(),
            'timelineable_id' => $event->model->getKey(),
            'record_sortable_date' => $event->model->timeline()->sortableBy(),
        ]);
    }
}

<?php

namespace Assist\Timeline\Listeners;

use Assist\Timeline\Models\Timeline;
use Assist\Timeline\Events\TimelineableRecordDeleted;

class RemoveRecordFromTimeline
{
    public function handle(TimelineableRecordDeleted $event): void
    {
        /** @var Educatable $educatable */
        $educatable = $event->educatable;

        cache()->forget("timeline.synced.{$educatable->getMorphClass()}.{$educatable->getKey()}");

        Timeline::where([
            'educatable_type' => $educatable->getMorphClass(),
            'educatable_id' => $educatable->getKey(),
            'timelineable_type' => $event->model->getMorphClass(),
            'timelineable_id' => $event->model->getKey(),
        ])->delete();
    }
}

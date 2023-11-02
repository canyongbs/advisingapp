<?php

namespace Assist\Timeline\Listeners;

use Assist\Timeline\Models\Timeline;
use Assist\Timeline\Events\TimelineableRecordCreated;

class AddRecordToTimeline
{
    public function handle(TimelineableRecordCreated $event): void
    {
        /** @var Model $entity */
        $entity = $event->entity;

        cache()->forget("timeline.synced.{$entity->getMorphClass()}.{$entity->getKey()}");

        Timeline::firstOrCreate([
            'entity_type' => $entity->getMorphClass(),
            'entity_id' => $entity->getKey(),
            'timelineable_type' => $event->timelineableModel->getMorphClass(),
            'timelineable_id' => $event->timelineableModel->getKey(),
            'record_sortable_date' => $event->timelineableModel->timeline()->sortableBy(),
        ]);
    }
}

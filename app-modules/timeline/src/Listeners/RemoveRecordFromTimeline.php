<?php

namespace Assist\Timeline\Listeners;

use Assist\Timeline\Models\Timeline;
use Illuminate\Database\Eloquent\Model;
use Assist\Timeline\Events\TimelineableRecordDeleted;

class RemoveRecordFromTimeline
{
    public function handle(TimelineableRecordDeleted $event): void
    {
        /** @var Model $entity */
        $entity = $event->entity;

        cache()->forget("timeline.synced.{$entity->getMorphClass()}.{$entity->getKey()}");

        Timeline::where([
            'entity_type' => $entity->getMorphClass(),
            'entity_id' => $entity->getKey(),
            'timelineable_type' => $event->timelineableModel->getMorphClass(),
            'timelineable_id' => $event->timelineableModel->getKey(),
        ])->delete();
    }
}

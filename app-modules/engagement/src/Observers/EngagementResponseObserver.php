<?php

namespace Assist\Engagement\Observers;

use Assist\Timeline\Models\Timeline;
use Assist\Engagement\Models\EngagementResponse;

class EngagementResponseObserver
{
    public function created(EngagementResponse $response): void
    {
        /** @var Student|Prospect $educatable */
        $educatable = $response->sender;

        // TODO Extract the timeline related actions
        cache()->forget("timeline.synced.{$educatable->getMorphClass()}.{$educatable->getKey()}");

        Timeline::firstOrCreate([
            'educatable_type' => $educatable->getMorphClass(),
            'educatable_id' => $educatable->getKey(),
            'timelineable_type' => $response->getMorphClass(),
            'timelineable_id' => $response->getKey(),
            'record_sortable_date' => $response->timeline()->sortableBy(),
        ]);
    }
}

<?php

namespace Assist\Engagement\Observers;

use Illuminate\Database\Eloquent\Model;
use Assist\Engagement\Models\EngagementResponse;
use Assist\Timeline\Events\TimelineableRecordCreated;
use Assist\Timeline\Events\TimelineableRecordDeleted;

class EngagementResponseObserver
{
    public function created(EngagementResponse $response): void
    {
        /** @var Model $educatable */
        $entity = $response->sender;

        TimelineableRecordCreated::dispatch($entity, $response);
    }

    public function deleted(EngagementResponse $response): void
    {
        /** @var Model $educatable */
        $entity = $response->sender;

        TimelineableRecordDeleted::dispatch($entity, $response);
    }
}

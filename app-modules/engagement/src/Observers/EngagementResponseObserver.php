<?php

namespace Assist\Engagement\Observers;

use Assist\Engagement\Models\EngagementResponse;
use Assist\Timeline\Events\TimelineableRecordCreated;
use Assist\Timeline\Events\TimelineableRecordDeleted;
use Assist\AssistDataModel\Models\Contracts\Educatable;

class EngagementResponseObserver
{
    public function created(EngagementResponse $response): void
    {
        /** @var Educatable $educatable */
        $educatable = $response->sender;

        TimelineableRecordCreated::dispatch($educatable, $response);
    }

    public function deleted(EngagementResponse $response): void
    {
        /** @var Educatable $educatable */
        $educatable = $response->sender;

        TimelineableRecordDeleted::dispatch($educatable, $response);
    }
}

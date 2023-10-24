<?php

namespace Assist\Engagement\Observers;

use Assist\Engagement\Models\Engagement;
use Assist\Timeline\Events\TimelineableRecordCreated;
use Assist\Timeline\Events\TimelineableRecordDeleted;
use Assist\AssistDataModel\Models\Contracts\Educatable;
use Assist\Notifications\Events\TriggeredAutoSubscription;

class EngagementObserver
{
    public function creating(Engagement $engagement): void
    {
        if (is_null($engagement->user_id) && ! is_null(auth()->user())) {
            $engagement->user_id = auth()->user()->id;
        }
    }

    public function saving(Engagement $engagement): void
    {
        $engagement->deliver_at = $engagement->deliver_at ?? now();
    }

    public function created(Engagement $engagement): void
    {
        if ($user = auth()->user()) {
            TriggeredAutoSubscription::dispatch($user, $engagement);
        }

        /** @var Educatable $educatable */
        $educatable = $engagement->recipient;

        TimelineableRecordCreated::dispatch($educatable, $engagement);
    }

    public function deleted(Engagement $engagement): void
    {
        /** @var Educatable $educatable */
        $educatable = $engagement->recipient;

        TimelineableRecordDeleted::dispatch($educatable, $engagement);
    }
}

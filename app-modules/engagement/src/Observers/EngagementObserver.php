<?php

namespace Assist\Engagement\Observers;

use Assist\Timeline\Models\Timeline;
use Assist\Engagement\Models\Engagement;
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

        /** @var Student|Prospect $educatable */
        $educatable = $engagement->recipient;

        // TODO Extract the timeline related actions
        cache()->forget("timeline.synced.{$educatable->getMorphClass()}.{$educatable->getKey()}");


        Timeline::firstOrCreate([
            'educatable_type' => $educatable->getMorphClass(),
            'educatable_id' => $educatable->getKey(),
            'timelineable_type' => $engagement->getMorphClass(),
            'timelineable_id' => $engagement->getKey(),
            'record_sortable_date' => $engagement->timeline()->sortableBy(),
        ]);
    }
}

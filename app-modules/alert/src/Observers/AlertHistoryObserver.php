<?php

namespace AdvisingApp\Alert\Observers;

use AdvisingApp\Alert\Histories\AlertHistory;
use AdvisingApp\Timeline\Events\TimelineableRecordCreated;

class AlertHistoryObserver
{
    public function created(AlertHistory $model): void
    {
        event(new TimelineableRecordCreated($model->subject->concern, $model));
    }
}

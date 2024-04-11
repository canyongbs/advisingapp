<?php

namespace AdvisingApp\Task\Observers;

use AdvisingApp\Task\Histories\TaskHistory;
use AdvisingApp\Timeline\Events\TimelineableRecordCreated;

class TaskHistoryObserver
{
    /**
     * Handle the TaskHistory "created" event.
     */
    public function created(TaskHistory $taskHistory): void
    {
        event(new TimelineableRecordCreated($taskHistory->subject->concern, $taskHistory));
    }
}

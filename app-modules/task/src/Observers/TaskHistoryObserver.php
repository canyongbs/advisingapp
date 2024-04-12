<?php

namespace AdvisingApp\Task\Observers;

use AdvisingApp\Task\Histories\TaskHistory;
use AdvisingApp\Timeline\Events\TimelineableRecordCreated;

class TaskHistoryObserver
{
    public function created(TaskHistory $taskHistory): void
    {
        if ($taskHistory->subject->concern) {
            event(new TimelineableRecordCreated($taskHistory->subject->concern, $taskHistory));
        }
    }
}

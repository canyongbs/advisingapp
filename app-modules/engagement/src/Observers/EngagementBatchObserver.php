<?php

namespace Assist\Engagement\Observers;

use Assist\Engagement\Models\EngagementBatch;

class EngagementBatchObserver
{
    public function creating(EngagementBatch $batch): void
    {
        if (is_null($batch->user_id) && ! is_null(auth()->user())) {
            $batch->user_id = auth()->user()->id;
        }
    }
}

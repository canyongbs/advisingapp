<?php

namespace AdvisingApp\Ai\Observers;

use AdvisingApp\Ai\Models\AiThread;

class AiThreadObserver
{
    public function trashed(AiThread $thread)
    {
        $thread->messages()->delete();
    }
}

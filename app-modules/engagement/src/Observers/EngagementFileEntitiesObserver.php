<?php

namespace Assist\Engagement\Observers;

use Assist\Engagement\Models\EngagementFileEntities;
use Assist\Notifications\Events\TriggeredAutoSubscription;

class EngagementFileEntitiesObserver
{
    public function created(EngagementFileEntities $engagementFileEntities): void
    {
        if ($user = auth()->user()) {
            TriggeredAutoSubscription::dispatch($user, $engagementFileEntities);
        }
    }
}

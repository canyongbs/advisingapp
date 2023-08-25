<?php

namespace Assist\Engagement\Observers;

use Assist\Engagement\Models\Engagement;
use Assist\Notifications\Events\TriggeredAutoSubscription;

class EngagementObserver
{
    public function created(Engagement $engagement): void
    {
        if ($user = auth()->user()) {
            TriggeredAutoSubscription::dispatch($user, $engagement);
        }
    }
}

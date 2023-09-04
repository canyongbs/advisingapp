<?php

namespace Assist\Case\Observers;

use Assist\Case\Models\ServiceRequestUpdate;
use Assist\Notifications\Events\TriggeredAutoSubscription;

class CaseUpdateObserver
{
    public function created(ServiceRequestUpdate $caseUpdate): void
    {
        if ($user = auth()->user()) {
            TriggeredAutoSubscription::dispatch($user, $caseUpdate);
        }
    }
}

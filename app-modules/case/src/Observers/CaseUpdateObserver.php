<?php

namespace Assist\Case\Observers;

use Assist\Case\Models\CaseUpdate;
use Assist\Notifications\Events\TriggeredAutoSubscription;

class CaseUpdateObserver
{
    public function created(CaseUpdate $caseUpdate): void
    {
        if ($user = auth()->user()) {
            TriggeredAutoSubscription::dispatch($user, $caseUpdate);
        }
    }
}

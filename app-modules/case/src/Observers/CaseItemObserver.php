<?php

namespace Assist\Case\Observers;

use Assist\Case\Models\ServiceRequest;
use Assist\Notifications\Events\TriggeredAutoSubscription;

class CaseItemObserver
{
    public function created(ServiceRequest $caseItem): void
    {
        if ($user = auth()->user()) {
            TriggeredAutoSubscription::dispatch($user, $caseItem);
        }
    }
}

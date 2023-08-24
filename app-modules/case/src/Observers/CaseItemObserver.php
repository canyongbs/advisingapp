<?php

namespace Assist\Case\Observers;

use Assist\Case\Models\CaseItem;
use Assist\Notifications\Events\TriggeredAutoSubscription;

class CaseItemObserver
{
    public function created(CaseItem $caseItem): void
    {
        if ($user = auth()->user()) {
            TriggeredAutoSubscription::dispatch($user, $caseItem);
        }
    }
}

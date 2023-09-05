<?php

namespace Assist\Case\Observers;

use Assist\Case\Models\ServiceRequestUpdate;
use Assist\Notifications\Events\TriggeredAutoSubscription;

class ServiceRequestUpdateObserver
{
    public function created(ServiceRequestUpdate $serviceRequestUpdate): void
    {
        if ($user = auth()->user()) {
            TriggeredAutoSubscription::dispatch($user, $serviceRequestUpdate);
        }
    }
}

<?php

namespace Assist\ServiceManagement\Observers;

use Assist\ServiceManagement\Models\ServiceRequestUpdate;
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

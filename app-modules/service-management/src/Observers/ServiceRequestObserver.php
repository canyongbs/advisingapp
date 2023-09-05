<?php

namespace Assist\ServiceManagement\Observers;

use Assist\ServiceManagement\Models\ServiceRequest;
use Assist\Notifications\Events\TriggeredAutoSubscription;

class ServiceRequestObserver
{
    public function created(ServiceRequest $serviceRequest): void
    {
        if ($user = auth()->user()) {
            TriggeredAutoSubscription::dispatch($user, $serviceRequest);
        }
    }
}

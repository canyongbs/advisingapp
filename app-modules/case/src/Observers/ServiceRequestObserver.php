<?php

namespace Assist\Case\Observers;

use Assist\Case\Models\ServiceRequest;
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

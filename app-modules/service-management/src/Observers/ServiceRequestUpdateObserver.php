<?php

namespace Assist\ServiceManagement\Observers;

use Assist\Timeline\Events\TimelineableRecordCreated;
use Assist\Timeline\Events\TimelineableRecordDeleted;
use Assist\ServiceManagement\Models\ServiceRequestUpdate;
use Assist\Notifications\Events\TriggeredAutoSubscription;

class ServiceRequestUpdateObserver
{
    public function created(ServiceRequestUpdate $serviceRequestUpdate): void
    {
        if ($user = auth()->user()) {
            TriggeredAutoSubscription::dispatch($user, $serviceRequestUpdate);
        }

        TimelineableRecordCreated::dispatch($serviceRequestUpdate->serviceRequest, $serviceRequestUpdate);
    }

    public function deleted(ServiceRequestUpdate $serviceRequestUpdate): void
    {
        TimelineableRecordDeleted::dispatch($serviceRequestUpdate->serviceRequest, $serviceRequestUpdate);
    }
}

<?php

namespace AdvisingApp\ServiceManagement\Observers;

use AdvisingApp\ServiceManagement\Models\ServiceRequestStatus;

class ServiceRequestStatusObserver
{
    public function deleted(ServiceRequestStatus $serviceRequestStatus): void
    {
        if ($serviceRequestStatus->serviceRequests()->doesntExist()) {
            $serviceRequestStatus->forceDelete();
        }
    }
}

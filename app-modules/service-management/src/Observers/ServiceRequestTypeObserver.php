<?php

namespace AdvisingApp\ServiceManagement\Observers;

use AdvisingApp\ServiceManagement\Models\ServiceRequestType;

class ServiceRequestTypeObserver
{
    public function deleted(ServiceRequestType $serviceRequestType): void
    {
        if ($serviceRequestType->serviceRequests()->doesntExist()) {
            $serviceRequestType->forceDelete();
        }
    }
}

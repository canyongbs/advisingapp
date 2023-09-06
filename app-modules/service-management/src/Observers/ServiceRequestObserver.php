<?php

namespace Assist\ServiceManagement\Observers;

use Assist\ServiceManagement\Models\ServiceRequest;
use Assist\Notifications\Events\TriggeredAutoSubscription;
use Assist\ServiceManagement\Exceptions\ServiceRequestNumberUpdateAttemptException;
use Assist\ServiceManagement\Services\ServiceRequestNumber\Contracts\ServiceRequestNumberGenerator;

class ServiceRequestObserver
{
    public function creating(ServiceRequest $serviceRequest): void
    {
        $serviceRequest->service_request_number ??= app(ServiceRequestNumberGenerator::class)->generate();
    }

    public function created(ServiceRequest $serviceRequest): void
    {
        if ($user = auth()->user()) {
            TriggeredAutoSubscription::dispatch($user, $serviceRequest);
        }
    }

    public function updating(ServiceRequest $serviceRequest): void
    {
        throw_if($serviceRequest->isDirty('service_request_number'), new ServiceRequestNumberUpdateAttemptException());
    }
}

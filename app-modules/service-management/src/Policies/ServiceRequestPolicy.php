<?php

namespace Assist\ServiceManagement\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\ServiceManagement\Models\ServiceRequest;

class ServiceRequestPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->can('service_request.view-any')
            ? Response::allow()
            : Response::deny('You do not have permission to view service requests.');
    }

    public function view(User $user, ServiceRequest $serviceRequest): Response
    {
        return $user->can('service_request.*.view') || $user->can("service_request.{$serviceRequest->id}.view")
            ? Response::allow()
            : Response::deny('You do not have permission to view this service request.');
    }

    public function create(User $user): Response
    {
        return $user->can('service_request.create')
            ? Response::allow()
            : Response::deny('You do not have permission to create service requests.');
    }

    public function update(User $user, ServiceRequest $serviceRequest): Response
    {
        return $user->can('service_request.*.update') || $user->can("service_request.{$serviceRequest->id}.update")
            ? Response::allow()
            : Response::deny('You do not have permission to update this service request.');
    }

    public function delete(User $user, ServiceRequest $serviceRequest): Response
    {
        return $user->can('service_request.*.delete') || $user->can("service_request.{$serviceRequest->id}.delete")
            ? Response::allow()
            : Response::deny('You do not have permission to delete this service request.');
    }

    public function restore(User $user, ServiceRequest $serviceRequest): Response
    {
        return $user->can('service_request.*.restore') || $user->can("service_request.{$serviceRequest->id}.restore")
            ? Response::allow()
            : Response::deny('You do not have permission to restore this service request.');
    }

    public function forceDelete(User $user, ServiceRequest $serviceRequest): Response
    {
        return $user->can('service_request.*.force-delete') || $user->can("service_request.{$serviceRequest->id}.force-delete")
            ? Response::allow()
            : Response::deny('You do not have permission to permanently delete this service request.');
    }
}

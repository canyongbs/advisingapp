<?php

namespace Assist\ServiceManagement\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\ServiceManagement\Models\ServiceRequestType;

class ServiceRequestTypePolicy
{
    public function viewAny(User $user): Response
    {
        return $user->can('service_request_type.view-any')
            ? Response::allow()
            : Response::deny('You do not have permissions to view service request types.');
    }

    public function view(User $user, ServiceRequestType $serviceRequestType): Response
    {
        return $user->can('service_request_type.*.view') || $user->can("service_request_type.{$serviceRequestType->id}.view")
            ? Response::allow()
            : Response::deny('You do not have permissions to view this service request type.');
    }

    public function create(User $user): Response
    {
        return $user->can('service_request_type.create')
            ? Response::allow()
            : Response::deny('You do not have permissions to create service request types.');
    }

    public function update(User $user, ServiceRequestType $serviceRequestType): Response
    {
        return $user->can('service_request_type.*.update') || $user->can("service_request_type.{$serviceRequestType->id}.update")
            ? Response::allow()
            : Response::deny('You do not have permissions to update this service request type.');
    }

    public function delete(User $user, ServiceRequestType $serviceRequestType): Response
    {
        return $user->can('service_request_type.*.delete') || $user->can("service_request_type.{$serviceRequestType->id}.delete")
            ? Response::allow()
            : Response::deny('You do not have permissions to delete this service request type.');
    }

    public function restore(User $user, ServiceRequestType $serviceRequestType): Response
    {
        return $user->can('service_request_type.*.restore') || $user->can("service_request_type.{$serviceRequestType->id}.restore")
            ? Response::allow()
            : Response::deny('You do not have permissions to restore this service request type.');
    }

    public function forceDelete(User $user, ServiceRequestType $serviceRequestType): Response
    {
        return $user->can('service_request_type.*.force-delete') || $user->can("service_request_type.{$serviceRequestType->id}.force-delete")
            ? Response::allow()
            : Response::deny('You do not have permissions to force delete this service request type.');
    }
}

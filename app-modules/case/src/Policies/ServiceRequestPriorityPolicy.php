<?php

namespace Assist\Case\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Case\Models\ServiceRequestPriority;

class ServiceRequestPriorityPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->can('service_request_priority.view-any')
            ? Response::allow()
            : Response::deny('You do not have permissions to view service request priorities.');
    }

    public function view(User $user, ServiceRequestPriority $serviceRequestPriority): Response
    {
        return $user->can('service_request_priority.*.view') || $user->can("service_request_priority.{$serviceRequestPriority->id}.view")
            ? Response::allow()
            : Response::deny('You do not have permissions to view this service request priority.');
    }

    public function create(User $user): Response
    {
        return $user->can('service_request_priority.create')
            ? Response::allow()
            : Response::deny('You do not have permissions to create service request priorities.');
    }

    public function update(User $user, ServiceRequestPriority $serviceRequestPriority): Response
    {
        return $user->can('service_request_priority.*.update') || $user->can("service_request_priority.{$serviceRequestPriority->id}.update")
            ? Response::allow()
            : Response::deny('You do not have permissions to update this service request priority.');
    }

    public function delete(User $user, ServiceRequestPriority $serviceRequestPriority): Response
    {
        return $user->can('service_request_priority.*.delete') || $user->can("service_request_priority.{$serviceRequestPriority->id}.delete")
            ? Response::allow()
            : Response::deny('You do not have permissions to delete this service request priority.');
    }

    public function restore(User $user, ServiceRequestPriority $serviceRequestPriority): Response
    {
        return $user->can('service_request_priority.*.restore') || $user->can("service_request_priority.{$serviceRequestPriority->id}.restore")
            ? Response::allow()
            : Response::deny('You do not have permissions to restore this service request priority.');
    }

    public function forceDelete(User $user, ServiceRequestPriority $serviceRequestPriority): Response
    {
        return $user->can('service_request_priority.*.force-delete') || $user->can("service_request_priority.{$serviceRequestPriority->id}.force-delete")
            ? Response::allow()
            : Response::deny('You do not have permissions to force delete this service request priority.');
    }
}

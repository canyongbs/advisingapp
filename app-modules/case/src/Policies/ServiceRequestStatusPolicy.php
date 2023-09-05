<?php

namespace Assist\Case\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Case\Models\ServiceRequestStatus;

class ServiceRequestStatusPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->can('service_request_status.view-any')
            ? Response::allow()
            : Response::deny('You do not have permissions to view service request statuses.');
    }

    public function view(User $user, ServiceRequestStatus $serviceRequestStatus): Response
    {
        return $user->can('service_request_status.*.view') || $user->can("service_request_status.{$serviceRequestStatus->id}.view")
            ? Response::allow()
            : Response::deny('You do not have permissions to view this service request status.');
    }

    public function create(User $user): Response
    {
        return $user->can('service_request_status.create')
            ? Response::allow()
            : Response::deny('You do not have permissions to create service request statuses.');
    }

    public function update(User $user, ServiceRequestStatus $serviceRequestStatus): Response
    {
        return $user->can('service_request_status.*.update') || $user->can("service_request_status.{$serviceRequestStatus->id}.update")
            ? Response::allow()
            : Response::deny('You do not have permissions to update this service request status.');
    }

    public function delete(User $user, ServiceRequestStatus $serviceRequestStatus): Response
    {
        return $user->can('service_request_status.*.delete') || $user->can("service_request_status.{$serviceRequestStatus->id}.delete")
            ? Response::allow()
            : Response::deny('You do not have permissions to delete this service request status.');
    }

    public function restore(User $user, ServiceRequestStatus $serviceRequestStatus): Response
    {
        return $user->can('service_request_status.*.restore') || $user->can("service_request_status.{$serviceRequestStatus->id}.restore")
            ? Response::allow()
            : Response::deny('You do not have permissions to restore this service request status.');
    }

    public function forceDelete(User $user, ServiceRequestStatus $serviceRequestStatus): Response
    {
        return $user->can('service_request_status.*.force-delete') || $user->can("service_request_status.{$serviceRequestStatus->id}.force-delete")
            ? Response::allow()
            : Response::deny('You do not have permissions to force delete this service request status.');
    }
}

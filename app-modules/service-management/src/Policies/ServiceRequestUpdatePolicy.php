<?php

namespace Assist\ServiceManagement\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\ServiceManagement\Models\ServiceRequestUpdate;

class ServiceRequestUpdatePolicy
{
    public function viewAny(User $user): Response
    {
        return $user->can('service_request_update.view-any')
            ? Response::allow()
            : Response::deny('You do not have permissions to view service request updates.');
    }

    public function view(User $user, ServiceRequestUpdate $serviceRequestUpdate): Response
    {
        return $user->can('service_request_update.*.view') || $user->can("service_request_update.{$serviceRequestUpdate->id}.view")
            ? Response::allow()
            : Response::deny('You do not have permissions to view this service request update.');
    }

    public function create(User $user): Response
    {
        return $user->can('service_request_update.create')
            ? Response::allow()
            : Response::deny('You do not have permissions to create service request updates.');
    }

    public function update(User $user, ServiceRequestUpdate $serviceRequestUpdate): Response
    {
        return $user->can('service_request_update.*.update') || $user->can("service_request_update.{$serviceRequestUpdate->id}.update")
            ? Response::allow()
            : Response::deny('You do not have permissions to update this service request update.');
    }

    public function delete(User $user, ServiceRequestUpdate $serviceRequestUpdate): Response
    {
        return $user->can('service_request_update.*.delete') || $user->can("service_request_update.{$serviceRequestUpdate->id}.delete")
            ? Response::allow()
            : Response::deny('You do not have permissions to delete this service request update.');
    }

    public function restore(User $user, ServiceRequestUpdate $serviceRequestUpdate): Response
    {
        return $user->can('service_request_update.*.restore') || $user->can("service_request_update.{$serviceRequestUpdate->id}.restore")
            ? Response::allow()
            : Response::deny('You do not have permissions to restore this service request update.');
    }

    public function forceDelete(User $user, ServiceRequestUpdate $serviceRequestUpdate): Response
    {
        return $user->can('service_request_update.*.force-delete') || $user->can("service_request_update.{$serviceRequestUpdate->id}.force-delete")
            ? Response::allow()
            : Response::deny('You do not have permissions to force delete this service request update.');
    }
}

<?php

namespace Assist\ServiceManagement\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\ServiceManagement\Models\ServiceRequestStatus;

class ServiceRequestStatusPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'service_request_status.view-any',
            denyResponse: 'You do not have permissions to view service request statuses.'
        );
    }

    public function view(User $user, ServiceRequestStatus $serviceRequestStatus): Response
    {
        return $user->canOrElse(
            abilities: ['service_request_status.*.view', "service_request_status.{$serviceRequestStatus->id}.view"],
            denyResponse: 'You do not have permissions to view this service request status.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'service_request_status.create',
            denyResponse: 'You do not have permissions to create service request statuses.'
        );
    }

    public function update(User $user, ServiceRequestStatus $serviceRequestStatus): Response
    {
        return $user->canOrElse(
            abilities: ['service_request_status.*.update', "service_request_status.{$serviceRequestStatus->id}.update"],
            denyResponse: 'You do not have permissions to update this service request status.'
        );
    }

    public function delete(User $user, ServiceRequestStatus $serviceRequestStatus): Response
    {
        return $user->canOrElse(
            abilities: ['service_request_status.*.delete', "service_request_status.{$serviceRequestStatus->id}.delete"],
            denyResponse: 'You do not have permissions to delete this service request status.'
        );
    }

    public function restore(User $user, ServiceRequestStatus $serviceRequestStatus): Response
    {
        return $user->canOrElse(
            abilities: ['service_request_status.*.restore', "service_request_status.{$serviceRequestStatus->id}.restore"],
            denyResponse: 'You do not have permissions to restore this service request status.'
        );
    }

    public function forceDelete(User $user, ServiceRequestStatus $serviceRequestStatus): Response
    {
        return $user->canOrElse(
            abilities: ['service_request_status.*.force-delete', "service_request_status.{$serviceRequestStatus->id}.force-delete"],
            denyResponse: 'You do not have permissions to force delete this service request status.'
        );
    }
}

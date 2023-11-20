<?php

namespace Assist\ServiceManagement\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\ServiceManagement\Models\ServiceRequest;

class ServiceRequestPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'service_request.view-any',
            denyResponse: 'You do not have permission to view service requests.'
        );
    }

    public function view(User $user, ServiceRequest $serviceRequest): Response
    {
        return $user->canOrElse(
            abilities: ['service_request.*.view', "service_request.{$serviceRequest->id}.view"],
            denyResponse: 'You do not have permission to view this service request.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'service_request.create',
            denyResponse: 'You do not have permission to create service requests.'
        );
    }

    public function update(User $user, ServiceRequest $serviceRequest): Response
    {
        return $user->canOrElse(
            abilities: ['service_request.*.update', "service_request.{$serviceRequest->id}.update"],
            denyResponse: 'You do not have permission to update this service request.'
        );
    }

    public function delete(User $user, ServiceRequest $serviceRequest): Response
    {
        return $user->canOrElse(
            abilities: ['service_request.*.delete', "service_request.{$serviceRequest->id}.delete"],
            denyResponse: 'You do not have permission to delete this service request.'
        );
    }

    public function restore(User $user, ServiceRequest $serviceRequest): Response
    {
        return $user->canOrElse(
            abilities: ['service_request.*.restore', "service_request.{$serviceRequest->id}.restore"],
            denyResponse: 'You do not have permission to restore this service request.'
        );
    }

    public function forceDelete(User $user, ServiceRequest $serviceRequest): Response
    {
        return $user->canOrElse(
            abilities: ['service_request.*.force-delete', "service_request.{$serviceRequest->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this service request.'
        );
    }
}

<?php

namespace Assist\ServiceManagement\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\ServiceManagement\Models\ServiceRequestPriority;

class ServiceRequestPriorityPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'service_request_priority.view-any',
            denyResponse: 'You do not have permissions to view service request priorities.'
        );
    }

    public function view(User $user, ServiceRequestPriority $serviceRequestPriority): Response
    {
        return $user->canOrElse(
            abilities: ['service_request_priority.*.view', "service_request_priority.{$serviceRequestPriority->id}.view"],
            denyResponse: 'You do not have permissions to view this service request priority.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'service_request_priority.create',
            denyResponse: 'You do not have permissions to create service request priorities.'
        );
    }

    public function update(User $user, ServiceRequestPriority $serviceRequestPriority): Response
    {
        return $user->canOrElse(
            abilities: ['service_request_priority.*.update', "service_request_priority.{$serviceRequestPriority->id}.update"],
            denyResponse: 'You do not have permissions to update this service request priority.'
        );
    }

    public function delete(User $user, ServiceRequestPriority $serviceRequestPriority): Response
    {
        return $user->canOrElse(
            abilities: ['service_request_priority.*.delete', "service_request_priority.{$serviceRequestPriority->id}.delete"],
            denyResponse: 'You do not have permissions to delete this service request priority.'
        );
    }

    public function restore(User $user, ServiceRequestPriority $serviceRequestPriority): Response
    {
        return $user->canOrElse(
            abilities: ['service_request_priority.*.restore', "service_request_priority.{$serviceRequestPriority->id}.restore"],
            denyResponse: 'You do not have permissions to restore this service request priority.'
        );
    }

    public function forceDelete(User $user, ServiceRequestPriority $serviceRequestPriority): Response
    {
        return $user->canOrElse(
            abilities: ['service_request_priority.*.force-delete', "service_request_priority.{$serviceRequestPriority->id}.force-delete"],
            denyResponse: 'You do not have permissions to force delete this service request priority.'
        );
    }
}

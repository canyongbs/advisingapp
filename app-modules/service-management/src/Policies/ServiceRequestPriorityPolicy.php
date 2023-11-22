<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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

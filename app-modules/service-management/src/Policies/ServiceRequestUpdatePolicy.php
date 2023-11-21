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
use Assist\ServiceManagement\Models\ServiceRequestUpdate;

class ServiceRequestUpdatePolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'service_request_update.view-any',
            denyResponse: 'You do not have permissions to view service request updates.'
        );
    }

    public function view(User $user, ServiceRequestUpdate $serviceRequestUpdate): Response
    {
        return $user->canOrElse(
            abilities: ['service_request_update.*.view', "service_request_update.{$serviceRequestUpdate->id}.view"],
            denyResponse: 'You do not have permissions to view this service request update.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'service_request_update.create',
            denyResponse: 'You do not have permissions to create service request updates.'
        );
    }

    public function update(User $user, ServiceRequestUpdate $serviceRequestUpdate): Response
    {
        return $user->canOrElse(
            abilities: ['service_request_update.*.update', "service_request_update.{$serviceRequestUpdate->id}.update"],
            denyResponse: 'You do not have permissions to update this service request update.'
        );
    }

    public function delete(User $user, ServiceRequestUpdate $serviceRequestUpdate): Response
    {
        return $user->canOrElse(
            abilities: ['service_request_update.*.delete', "service_request_update.{$serviceRequestUpdate->id}.delete"],
            denyResponse: 'You do not have permissions to delete this service request update.'
        );
    }

    public function restore(User $user, ServiceRequestUpdate $serviceRequestUpdate): Response
    {
        return $user->canOrElse(
            abilities: ['service_request_update.*.restore', "service_request_update.{$serviceRequestUpdate->id}.restore"],
            denyResponse: 'You do not have permissions to restore this service request update.'
        );
    }

    public function forceDelete(User $user, ServiceRequestUpdate $serviceRequestUpdate): Response
    {
        return $user->canOrElse(
            abilities: ['service_request_update.*.force-delete', "service_request_update.{$serviceRequestUpdate->id}.force-delete"],
            denyResponse: 'You do not have permissions to force delete this service request update.'
        );
    }
}

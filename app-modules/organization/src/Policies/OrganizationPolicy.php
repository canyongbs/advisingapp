<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Organization\Policies;

use AdvisingApp\Organization\Models\Organization;
use AdvisingApp\Prospect\Models\Prospect;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;

class OrganizationPolicy
{
    public function before(Authenticatable $authenticatable): ?Response
    {
        if (! $authenticatable->hasLicense(Prospect::getLicenseType())) {
            return Response::deny('You are not licensed for the Recruitment CRM.');
        }

        return null;
    }
    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'organization.view-any',
            denyResponse: 'You do not have permission to view organizations.'
        );
    }

    public function view(Authenticatable $authenticatable, Organization $organization): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['organization.*.view'],
            denyResponse: 'You do not have permission to view this organization.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'organization.create',
            denyResponse: 'You do not have permission to create organizations.'
        );
    }

    public function update(Authenticatable $authenticatable, Organization $organization): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['organization.*.update'],
            denyResponse: 'You do not have permission to update this organization.'
        );
    }

    public function delete(Authenticatable $authenticatable, Organization $organization): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['organization.*.delete'],
            denyResponse: 'You do not have permission to delete this organization.'
        );
    }

    public function deleteAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['organization.*.delete'],
            denyResponse: 'You do not have permission to delete any organization.'
        );
    }

    public function restore(Authenticatable $authenticatable, Organization $organization): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['organization.*.restore'],
            denyResponse: 'You do not have permission to restore this organization.'
        );
    }

    public function restoreAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['organization.*.restore'],
            denyResponse: 'You do not have permission to restore any organization.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, Organization $organization): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['organization.*.force-delete'],
            denyResponse: 'You do not have permission to force delete this organization.'
        );
    }

    public function forceDeleteAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['organization.*.force-delete'],
            denyResponse: 'You do not have permission to force delete any organization.'
        );
    }
}

<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Concern\Policies;

use AdvisingApp\Concern\Models\Concern;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;

class ConcernPolicy
{
    public function before(Authenticatable $authenticatable): ?Response
    {
        if (! $authenticatable->hasAnyLicense([Student::getLicenseType(), Prospect::getLicenseType()])) {
            return Response::deny('You are not licensed for the Retention or Recruitment CRM.');
        }

        return null;
    }

    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'concern.view-any',
            denyResponse: 'You do not have permission to view concerns.'
        );
    }

    public function view(Authenticatable $authenticatable, Concern $concern): Response
    {
        if (! $authenticatable->hasLicense($concern->concern?->getLicenseType())) {
            return Response::deny('You do not have permission to view this concern.');
        }

        return $authenticatable->canOrElse(
            abilities: ['concern.*.view'],
            denyResponse: 'You do not have permission to view this concern.'
        );
    }

    public function create(Authenticatable $authenticatable, ?Prospect $prospect = null): Response
    {
        if ($prospect?->student()->exists()) {
            return Response::deny('You cannot create concerns for a Prospect that has been converted to a Student.');
        }

        return $authenticatable->canOrElse(
            abilities: 'concern.create',
            denyResponse: 'You do not have permission to create concerns.'
        );
    }

    public function update(Authenticatable $authenticatable, Concern $concern): Response
    {
        if ($concern->concern_type === (new Prospect())->getMorphClass() && filled($concern->concern->student_id)) {
            return Response::deny('You cannot edit this concern as the related Prospect has been converted to a Student.');
        }

        if (! $authenticatable->hasLicense($concern->concern?->getLicenseType())) {
            return Response::deny('You do not have permission to update this concern.');
        }

        return $authenticatable->canOrElse(
            abilities: ['concern.*.update'],
            denyResponse: 'You do not have permission to update this concern.'
        );
    }

    public function delete(Authenticatable $authenticatable, Concern $concern): Response
    {
        if (! $authenticatable->hasLicense($concern->concern?->getLicenseType())) {
            return Response::deny('You do not have permission to delete this concern.');
        }

        return $authenticatable->canOrElse(
            abilities: ['concern.*.delete'],
            denyResponse: 'You do not have permission to delete this concern.'
        );
    }

    public function restore(Authenticatable $authenticatable, Concern $concern): Response
    {
        if (! $authenticatable->hasLicense($concern->concern?->getLicenseType())) {
            return Response::deny('You do not have permission to restore this concern.');
        }

        return $authenticatable->canOrElse(
            abilities: ['concern.*.restore'],
            denyResponse: 'You do not have permission to restore this concern.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, Concern $concern): Response
    {
        if (! $authenticatable->hasLicense($concern->concern?->getLicenseType())) {
            return Response::deny('You do not have permission to permanently delete this concern.');
        }

        return $authenticatable->canOrElse(
            abilities: ['concern.*.force-delete'],
            denyResponse: 'You do not have permission to permanently delete this concern.'
        );
    }
}

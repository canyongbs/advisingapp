<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\CaseManagement\Policies;

use App\Enums\Feature;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;
use App\Concerns\PerformsFeatureChecks;
use App\Concerns\PerformsLicenseChecks;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\CaseManagement\Models\ChangeRequestType;
use App\Policies\Contracts\PerformsChecksBeforeAuthorization;

class ChangeRequestTypePolicy implements PerformsChecksBeforeAuthorization
{
    use PerformsFeatureChecks;
    use PerformsLicenseChecks;

    public function before(Authenticatable $authenticatable): ?Response
    {
        if (! is_null($response = $this->hasAnyLicense($authenticatable, [LicenseType::RetentionCrm, LicenseType::RecruitmentCrm]))) {
            return $response;
        }

        if (! is_null($response = $this->hasFeatures())) {
            return $response;
        }

        return null;
    }

    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'product_admin.view-any',
            denyResponse: 'You do not have permission to view change request types.'
        );
    }

    public function view(Authenticatable $authenticatable, ChangeRequestType $changeRequestType): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["product_admin.{$changeRequestType->id}.view"],
            denyResponse: 'You do not have permission to view this change request type.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'product_admin.create',
            denyResponse: 'You do not have permission to create change request types.'
        );
    }

    public function update(Authenticatable $authenticatable, ChangeRequestType $changeRequestType): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["product_admin.{$changeRequestType->id}.update"],
            denyResponse: 'You do not have permission to update this change request type.'
        );
    }

    public function delete(Authenticatable $authenticatable, ChangeRequestType $changeRequestType): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["product_admin.{$changeRequestType->id}.delete"],
            denyResponse: 'You do not have permission to delete this change request type.'
        );
    }

    public function restore(Authenticatable $authenticatable, ChangeRequestType $changeRequestType): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["product_admin.{$changeRequestType->id}.restore"],
            denyResponse: 'You do not have permission to restore this change request type.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, ChangeRequestType $changeRequestType): Response
    {
        if ($changeRequestType->changeRequests()->exists()) {
            return Response::deny('You cannot force delete this change request type because it has associated change requests.');
        }

        return $authenticatable->canOrElse(
            abilities: ["product_admin.{$changeRequestType->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this change request type.'
        );
    }

    protected function requiredFeatures(): array
    {
        return [Feature::ServiceManagement];
    }
}

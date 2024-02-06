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

namespace AdvisingApp\Analytics\Policies;

use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;
use App\Concerns\PerformsLicenseChecks;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Analytics\Models\AnalyticsResourceCategory;
use App\Policies\Contracts\PerformsChecksBeforeAuthorization;

class AnalyticsResourceCategoryPolicy implements PerformsChecksBeforeAuthorization
{
    use PerformsLicenseChecks;

    public function before(Authenticatable $authenticatable): ?Response
    {
        if (! is_null($response = $this->hasAnyLicense($authenticatable, [LicenseType::RetentionCrm, LicenseType::RecruitmentCrm]))) {
            return $response;
        }

        return null;
    }

    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'analytics_resource_category.view-any',
            denyResponse: 'You do not have permission to view analytics resource categories.'
        );
    }

    public function view(Authenticatable $authenticatable, AnalyticsResourceCategory $analyticsResourceCategory): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['analytics_resource_category.*.view', "analytics_resource_category.{$analyticsResourceCategory->id}.view"],
            denyResponse: 'You do not have permission to view this analytics resource category.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'analytics_resource_category.create',
            denyResponse: 'You do not have permission to create analytics resource categories.'
        );
    }

    public function update(Authenticatable $authenticatable, AnalyticsResourceCategory $analyticsResourceCategory): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['analytics_resource_category.*.update', "analytics_resource_category.{$analyticsResourceCategory->id}.update"],
            denyResponse: 'You do not have permission to update this analytics resource category.'
        );
    }

    public function delete(Authenticatable $authenticatable, AnalyticsResourceCategory $analyticsResourceCategory): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['analytics_resource_category.*.delete', "analytics_resource_category.{$analyticsResourceCategory->id}.delete"],
            denyResponse: 'You do not have permission to delete this analytics resource category.'
        );
    }

    public function restore(Authenticatable $authenticatable, AnalyticsResourceCategory $analyticsResourceCategory): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['analytics_resource_category.*.restore', "analytics_resource_category.{$analyticsResourceCategory->id}.restore"],
            denyResponse: 'You do not have permission to restore this analytics resource category.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, AnalyticsResourceCategory $analyticsResourceCategory): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['analytics_resource_category.*.force-delete', "analytics_resource_category.{$analyticsResourceCategory->id}.force-delete"],
            denyResponse: 'You do not have permission to force delete this analytics resource category.'
        );
    }
}

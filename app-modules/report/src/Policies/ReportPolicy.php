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

namespace AdvisingApp\Report\Policies;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Report\Models\Report;
use App\Concerns\PerformsLicenseChecks;
use App\Models\Authenticatable;
use App\Models\User;
use App\Policies\Contracts\PerformsChecksBeforeAuthorization;
use Illuminate\Auth\Access\Response;

class ReportPolicy implements PerformsChecksBeforeAuthorization
{
    use PerformsLicenseChecks;

    public function before(Authenticatable $authenticatable): ?Response
    {
        if (
            (! $authenticatable->hasAnyLicense([LicenseType::RetentionCrm, LicenseType::RecruitmentCrm])) &&
            $authenticatable->cannot('viewAny', User::class)
        ) {
            return Response::deny('You can not access this resource.');
        }

        return null;
    }

    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'report.view-any',
            denyResponse: 'You do not have permission to view reports.'
        );
    }

    public function view(Authenticatable $authenticatable, Report $report): Response
    {
        if (! $report->model->canBeAccessed($authenticatable)) {
            return Response::deny('You do not have permission to view this report.');
        }

        return $authenticatable->canOrElse(
            abilities: ["report.{$report->getKey()}.view"],
            denyResponse: 'You do not have permission to view this report.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'report.create',
            denyResponse: 'You do not have permission to create reports.'
        );
    }

    public function update(Authenticatable $authenticatable, Report $report): Response
    {
        if (! $report->model->canBeAccessed($authenticatable)) {
            return Response::deny('You do not have permission to update this report.');
        }

        return $authenticatable->canOrElse(
            abilities: ["report.{$report->getKey()}.update"],
            denyResponse: 'You do not have permission to update this report.'
        );
    }

    public function delete(Authenticatable $authenticatable, Report $report): Response
    {
        if (! $report->model->canBeAccessed($authenticatable)) {
            return Response::deny('You do not have permission to delete this report.');
        }

        return $authenticatable->canOrElse(
            abilities: ["report.{$report->getKey()}.delete"],
            denyResponse: 'You do not have permission to delete this report.'
        );
    }

    public function restore(Authenticatable $authenticatable, Report $report): Response
    {
        if (! $report->model->canBeAccessed($authenticatable)) {
            return Response::deny('You do not have permission to restore this report.');
        }

        return $authenticatable->canOrElse(
            abilities: ["report.{$report->getKey()}.restore"],
            denyResponse: 'You do not have permission to restore this report.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, Report $report): Response
    {
        if (! $report->model->canBeAccessed($authenticatable)) {
            return Response::deny('You do not have permission to permanently delete this report.');
        }

        return $authenticatable->canOrElse(
            abilities: ["report.{$report->getKey()}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this report.'
        );
    }
}

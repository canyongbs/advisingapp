<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Project\Policies;

use AdvisingApp\Project\Models\ProjectMilestoneStatus;
use App\Concerns\PerformsFeatureChecks;
use App\Enums\Feature;
use App\Models\Authenticatable;
use App\Policies\Contracts\PerformsChecksBeforeAuthorization;
use App\Support\FeatureAccessResponse;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;

class ProjectMilestoneStatusPolicy implements PerformsChecksBeforeAuthorization
{
    use PerformsFeatureChecks;

    public function before(Authenticatable $authenticatable): ?Response
    {
        if (! Gate::check(
            collect($this->requiredFeatures())->map(fn (Feature $feature) => $feature->getGateName())
        )) {
            return FeatureAccessResponse::deny();
        }

        return null;
    }

    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'settings.view-any',
            denyResponse: 'You do not have permissions to view project milestone statuses.'
        );
    }

    public function view(Authenticatable $authenticatable, ProjectMilestoneStatus $projectMilestoneStatus): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'settings.*.view',
            denyResponse: 'You do not have permissions to view this project milestone status.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'settings.create',
            denyResponse: 'You do not have permissions to create project milestone statuses.'
        );
    }

    public function update(Authenticatable $authenticatable, ProjectMilestoneStatus $projectMilestoneStatus): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'settings.*.update',
            denyResponse: 'You do not have permissions to update this project milestone status.'
        );
    }

    public function delete(Authenticatable $authenticatable, ProjectMilestoneStatus $projectMilestoneStatus): Response
    {
        if ($projectMilestoneStatus->milestones()->exists()) {
            return Response::deny('You cannot delete this project milestone status because it has associated project milestones.');
        }

        return $authenticatable->canOrElse(
            abilities: 'settings.*.delete',
            denyResponse: 'You do not have permissions to delete this project milestone status.'
        );
    }

    public function restore(Authenticatable $authenticatable, ProjectMilestoneStatus $projectMilestoneStatus): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'settings.*.restore',
            denyResponse: 'You do not have permissions to restore this project milestone status.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, ProjectMilestoneStatus $projectMilestoneStatus): Response
    {
        if ($projectMilestoneStatus->milestones()->exists()) {
            return Response::deny('You cannot force delete this project milestone status because it has associated project milestones.');
        }

        return $authenticatable->canOrElse(
            abilities: 'settings.*.force-delete',
            denyResponse: 'You do not have permissions to force delete this project milestone status.'
        );
    }

    protected function requiredFeatures(): array
    {
        return [Feature::ProjectManagement];
    }
}

<?php

namespace AdvisingApp\Project\Policies;

use AdvisingApp\Project\Models\ProjectMilestoneStatus;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;

class ProjectMilestoneStatusPolicy
{
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
}

<?php

namespace AdvisingApp\Project\Policies;

use AdvisingApp\Project\Models\Project;
use AdvisingApp\Project\Models\ProjectMilestone;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;

class ProjectMilestonePolicy
{
    public function viewAny(Authenticatable $authenticatable, Project $project): Response
    {
        if ($authenticatable->cannot('view', $project)) {
            return Response::deny('You do not have permission to view milestones.');
        }

        return Response::allow();
    }

    public function view(Authenticatable $authenticatable, ProjectMilestone $projectMilestone): Response
    {
        if ($authenticatable->cannot('view', $projectMilestone->project)) {
            return Response::deny('You do not have permission to view this milestone.');
        }

        return Response::allow();
    }

    public function create(Authenticatable $authenticatable, Project $project): Response
    {
        if ($authenticatable->cannot('update', $project)) {
            return Response::deny('You do not have permission to create milestones.');
        }

        return Response::allow();
    }

    public function update(Authenticatable $authenticatable, ProjectMilestone $projectMilestone): Response
    {
        if ($authenticatable->cannot('update', $projectMilestone->project)) {
            return Response::deny('You do not have permission to update this milestone.');
        }

        return Response::allow();
    }

    public function delete(Authenticatable $authenticatable, ProjectMilestone $projectMilestone): Response
    {
        if ($authenticatable->cannot('update', $projectMilestone->project)) {
            return Response::deny('You do not have permission to delete this milestone.');
        }

        return Response::allow();
    }
}

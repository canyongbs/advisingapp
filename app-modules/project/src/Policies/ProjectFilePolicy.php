<?php

namespace AdvisingApp\Project\Policies;

use AdvisingApp\Project\Models\Project;
use AdvisingApp\Project\Models\ProjectFile;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;

class ProjectFilePolicy
{
    public function viewAny(Authenticatable $authenticatable, Project $project): Response
    {
        if ($authenticatable->cannot('view', $project)) {
            return Response::deny('You do not have permission to view files.');
        }

        return Response::allow();
    }

    public function view(Authenticatable $authenticatable, ProjectFile $projectFile): Response
    {
        if ($authenticatable->cannot('view', $projectFile->project)) {
            return Response::deny('You do not have permission to view file.');
        }

        return Response::allow();
    }

    public function create(Authenticatable $authenticatable, Project $project): Response
    {
        if ($authenticatable->cannot('update', $project)) {
            return Response::deny('You do not have permission to create file.');
        }

        return Response::allow();
    }

    public function update(Authenticatable $authenticatable, ProjectFile $projectFile): Response
    {
        if ($authenticatable->cannot('update', $projectFile->project)) {
            return Response::deny('You do not have permission to update this file.');
        }

        return Response::allow();
    }

    public function delete(Authenticatable $authenticatable, ProjectFile $projectFile): Response
    {
        if ($authenticatable->cannot('update', $projectFile->project)) {
            return Response::deny('You do not have permission to delete this file.');
        }

        return Response::allow();
    }
}

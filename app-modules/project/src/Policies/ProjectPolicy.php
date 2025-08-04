<?php

namespace AdvisingApp\Project\Policies;

use AdvisingApp\Project\Models\Project;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'project.view-any',
            denyResponse: 'You do not have permission to view projects.'
        );
    }

    public function view(Authenticatable $authenticatable, Project $project): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'project.*.view',
            denyResponse: 'You do not have permission to view this project.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'project.create',
            denyResponse: 'You do not have permission to create projects.'
        );
    }

    public function update(Authenticatable $authenticatable, Project $project): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['project.*.update'],
            denyResponse: 'You do not have permission to update this project.'
        );
    }

    public function delete(Authenticatable $authenticatable, Project $project): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'project.*.delete',
            denyResponse: 'You do not have permission to delete this project.'
        );
    }

    public function restore(Authenticatable $authenticatable, Project $project): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'project.*.restore',
            denyResponse: 'You do not have permission to restore this project.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, Project $project): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'project.*.force-delete',
            denyResponse: 'You do not have permission to permanently delete this project.'
        );
    }
}

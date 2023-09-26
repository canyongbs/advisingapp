<?php

namespace Assist\Team\Policies;

use App\Models\User;
use Assist\Team\Models\Team;
use Illuminate\Auth\Access\Response;

class TeamPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'team.view-any',
            denyResponse: 'You do not have permission to view interactions.'
        );
    }

    public function view(User $user, Team $team): Response
    {
        return $user->canOrElse(
            abilities: ['team.*.view', "team.{$team->id}.view"],
            denyResponse: 'You do not have permission to view this team.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'team.create',
            denyResponse: 'You do not have permission to create interactions.'
        );
    }

    public function update(User $user, Team $team): Response
    {
        return $user->canOrElse(
            abilities: ['team.*.update', "team.{$team->id}.update"],
            denyResponse: 'You do not have permission to update this team.'
        );
    }

    public function delete(User $user, Team $team): Response
    {
        return $user->canOrElse(
            abilities: ['team.*.delete', "team.{$team->id}.delete"],
            denyResponse: 'You do not have permission to delete this team.'
        );
    }

    public function restore(User $user, Team $team): Response
    {
        return $user->canOrElse(
            abilities: ['team.*.restore', "team.{$team->id}.restore"],
            denyResponse: 'You do not have permission to restore this team.'
        );
    }

    public function forceDelete(User $user, Team $team): Response
    {
        return $user->canOrElse(
            abilities: ['team.*.force-delete', "team.{$team->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this team.'
        );
    }
}

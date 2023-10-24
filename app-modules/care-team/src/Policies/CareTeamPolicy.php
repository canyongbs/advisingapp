<?php

namespace Assist\Notifications\Policies;

use App\Models\User;
use Assist\CareTeam\Models\CareTeam;
use Illuminate\Auth\Access\Response;

class CareTeamPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'care_team.view-any',
            denyResponse: 'You do not have permission to view care teams.'
        );
    }

    public function view(User $user, CareTeam $careTeam): Response
    {
        return $user->canOrElse(
            abilities: ['care_team.*.view', "care_team.{$careTeam->id}.view"],
            denyResponse: 'You do not have permission to view this care team.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'care_team.create',
            denyResponse: 'You do not have permission to create care teams.'
        );
    }

    public function update(User $user, CareTeam $careTeam): Response
    {
        return $user->canOrElse(
            abilities: ['care_team.*.update', "care_team.{$careTeam->id}.update"],
            denyResponse: 'You do not have permission to update this care team.'
        );
    }

    public function delete(User $user, CareTeam $careTeam): Response
    {
        return $user->canOrElse(
            abilities: ['care_team.*.delete', "care_team.{$careTeam->id}.delete"],
            denyResponse: 'You do not have permission to delete this care team.'
        );
    }

    public function restore(User $user, CareTeam $careTeam): Response
    {
        return $user->canOrElse(
            abilities: ['care_team.*.restore', "care_team.{$careTeam->id}.restore"],
            denyResponse: 'You do not have permission to restore this care team.'
        );
    }

    public function forceDelete(User $user, CareTeam $careTeam): Response
    {
        return $user->canOrElse(
            abilities: ['care_team.*.force-delete', "care_team.{$careTeam->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this care team.'
        );
    }
}

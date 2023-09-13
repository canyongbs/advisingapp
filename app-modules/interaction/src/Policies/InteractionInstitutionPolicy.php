<?php

namespace Assist\Interaction\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Interaction\Models\InteractionInstitution;

class InteractionInstitutionPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'interaction_institution.view-any',
            denyResponse: 'You do not have permission to view interaction institutions.'
        );
    }

    public function view(User $user, InteractionInstitution $institution): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_institution.*.view', "interaction_institution.{$institution->id}.view"],
            denyResponse: 'You do not have permission to view this interaction institution.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'interaction_institution.create',
            denyResponse: 'You do not have permission to create interaction institutions.'
        );
    }

    public function update(User $user, InteractionInstitution $institution): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_institution.*.update', "interaction_institution.{$institution->id}.update"],
            denyResponse: 'You do not have permission to update this interaction institution.'
        );
    }

    public function delete(User $user, InteractionInstitution $institution): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_institution.*.delete', "interaction_institution.{$institution->id}.delete"],
            denyResponse: 'You do not have permission to delete this interaction institution.'
        );
    }

    public function restore(User $user, InteractionInstitution $institution): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_institution.*.restore', "interaction_institution.{$institution->id}.restore"],
            denyResponse: 'You do not have permission to restore this interaction institution.'
        );
    }

    public function forceDelete(User $user, InteractionInstitution $institution): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_institution.*.force-delete', "interaction_institution.{$institution->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this interaction institution.'
        );
    }
}

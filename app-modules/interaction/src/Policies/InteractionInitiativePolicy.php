<?php

namespace AdvisingApp\Interaction\Policies;

use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Interaction\Models\InteractionInitiative;

class InteractionInitiativePolicy
{
    public function before(Authenticatable $authenticatable): ?Response
    {
        if (! $authenticatable->hasAnyLicense([Student::getLicenseType(), Prospect::getLicenseType()])) {
            return Response::deny('You are not licensed for the Retention or Recruitment CRM.');
        }

        return null;
    }

    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'interaction_initiative.view-any',
            denyResponse: 'You do not have permission to view interaction initiatives.'
        );
    }

    public function view(Authenticatable $authenticatable, InteractionInitiative $initiative): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['interaction_initiative.*.view', "interaction_initiative.{$initiative->id}.view"],
            denyResponse: 'You do not have permission to view this interaction initiative.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'interaction_initiative.create',
            denyResponse: 'You do not have permission to create interaction initiatives.'
        );
    }

    public function update(Authenticatable $authenticatable, InteractionInitiative $initiative): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['interaction_initiative.*.update', "interaction_initiative.{$initiative->id}.update"],
            denyResponse: 'You do not have permission to update this interaction initiative.'
        );
    }

    public function delete(Authenticatable $authenticatable, InteractionInitiative $initiative): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['interaction_initiative.*.delete', "interaction_initiative.{$initiative->id}.delete"],
            denyResponse: 'You do not have permission to delete this interaction initiative.'
        );
    }

    public function restore(Authenticatable $authenticatable, InteractionInitiative $initiative): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['interaction_initiative.*.restore', "interaction_initiative.{$initiative->id}.restore"],
            denyResponse: 'You do not have permission to restore this interaction initiative.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, InteractionInitiative $initiative): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['interaction_initiative.*.force-delete', "interaction_initiative.{$initiative->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this interaction initiative.'
        );
    }
}

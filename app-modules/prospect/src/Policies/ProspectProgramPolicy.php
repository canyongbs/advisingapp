<?php

namespace AdvisingApp\Prospect\Policies;

use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectProgram;

class ProspectProgramPolicy
{
    public function before(Authenticatable $authenticatable): ?Response
    {
        if (! $authenticatable->hasLicense(Prospect::getLicenseType())) {
            return Response::deny('You are not licensed for the Recruitment CRM.');
        }

        return null;
    }

    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'prospect_program.view-any',
            denyResponse: 'You do not have permission to view prospect program.'
        );
    }

    public function view(Authenticatable $authenticatable, ProspectProgram $prospectProgram): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['prospect_program.*.view', "prospect_program.{$prospectProgram->id}.view"],
            denyResponse: 'You do not have permission to view this prospect program.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'prospect_program.create',
            denyResponse: 'You do not have permission to create prospect program.'
        );
    }

    public function update(Authenticatable $authenticatable, ProspectProgram $prospectProgram): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['prospect_program.*.update', "prospect_program.{$prospectProgram->id}.update"],
            denyResponse: 'You do not have permission to update this prospect program.'
        );
    }

    public function delete(Authenticatable $authenticatable, ProspectProgram $prospectProgram): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['prospect_program.*.delete', "prospect_program.{$prospectProgram->id}.delete"],
            denyResponse: 'You do not have permission to delete this prospect program.'
        );
    }

    public function restore(Authenticatable $authenticatable, ProspectProgram $prospectProgram): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['prospect_program.*.restore', "prospect_program.{$prospectProgram->id}.restore"],
            denyResponse: 'You do not have permission to restore this prospect program.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, ProspectProgram $prospectProgram): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['prospect_program.*.force-delete', "prospect_program.{$prospectProgram->id}.force-delete"],
            denyResponse: 'You do not have permission to force delete this prospect program.'
        );
    }
}

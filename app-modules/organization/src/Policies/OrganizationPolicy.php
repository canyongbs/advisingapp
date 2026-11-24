<?php

namespace AdvisingApp\Organization\Policies;

use AdvisingApp\Organization\Models\Organization;
use AdvisingApp\Prospect\Models\Prospect;
use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;

class OrganizationPolicy
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
            abilities: 'organization.view-any',
            denyResponse: 'You do not have permission to view organizations.'
        );
    }

    public function view(Authenticatable $authenticatable, Organization $organization): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['organization.*.view'],
            denyResponse: 'You do not have permission to view this organization.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'organization.create',
            denyResponse: 'You do not have permission to create organizations.'
        );
    }

    public function update(Authenticatable $authenticatable, Organization $organization): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['organization.*.update'],
            denyResponse: 'You do not have permission to update this organization.'
        );
    }

    public function delete(Authenticatable $authenticatable, Organization $organization): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['organization.*.delete'],
            denyResponse: 'You do not have permission to delete this organization.'
        );
    }

    public function deleteAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['organization.*.delete'],
            denyResponse: 'You do not have permission to delete any organization.'
        );
    }

    public function restore(Authenticatable $authenticatable, Organization $organization): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['organization.*.restore'],
            denyResponse: 'You do not have permission to restore this organization.'
        );
    }

    public function restoreAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['organization.*.restore'],
            denyResponse: 'You do not have permission to restore any organization.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, Organization $organization): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['organization.*.force-delete'],
            denyResponse: 'You do not have permission to force delete this organization.'
        );
    }

    public function forceDeleteAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['organization.*.force-delete'],
            denyResponse: 'You do not have permission to force delete any organization.'
        );
    }
}

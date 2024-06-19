<?php

namespace AdvisingApp\Prospect\Policies;

use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectCategory;

class ProspectCategoryPolicy
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
            abilities: 'prospect_category.view-any',
            denyResponse: 'You do not have permission to view prospect category.'
        );
    }

    public function view(Authenticatable $authenticatable, ProspectCategory $prospectCategory): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['prospect_category.*.view', "prospect_category.{$prospectCategory->id}.view"],
            denyResponse: 'You do not have permission to view this prospect category.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'prospect_category.create',
            denyResponse: 'You do not have permission to create prospect category.'
        );
    }

    public function update(Authenticatable $authenticatable, ProspectCategory $prospectCategory): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['prospect_category.*.update', "prospect_category.{$prospectCategory->id}.update"],
            denyResponse: 'You do not have permission to update this prospect category.'
        );
    }

    public function delete(Authenticatable $authenticatable, ProspectCategory $prospectCategory): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['prospect_category.*.delete', "prospect_category.{$prospectCategory->id}.delete"],
            denyResponse: 'You do not have permission to delete this prospect category.'
        );
    }

    public function restore(Authenticatable $authenticatable, ProspectCategory $prospectCategory): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['prospect_category.*.restore', "prospect_category.{$prospectCategory->id}.restore"],
            denyResponse: 'You do not have permission to restore this prospect category.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, ProspectCategory $prospectCategory): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['prospect_category.*.force-delete', "prospect_category.{$prospectCategory->id}.force-delete"],
            denyResponse: 'You do not have permission to force delete this prospect category.'
        );
    }
}

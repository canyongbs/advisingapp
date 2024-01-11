<?php

namespace AdvisingApp\Authorization\Policies;

use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;
use AdvisingApp\Authorization\Models\License;

class LicensePolicy
{
    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'license.view-any',
            denyResponse: 'You do not have permission to view licenses.'
        );
    }

    public function view(Authenticatable $authenticatable, License $license): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['license.*.view', "license.{$license->id}.view"],
            denyResponse: 'You do not have permission to view this license.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'license.create',
            denyResponse: 'You do not have permission to create licenses.'
        );
    }

    public function update(Authenticatable $authenticatable, License $license): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['license.*.update', "license.{$license->id}.update"],
            denyResponse: 'You do not have permission to update this license.'
        );
    }

    public function delete(Authenticatable $authenticatable, License $license): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['license.*.delete', "license.{$license->id}.delete"],
            denyResponse: 'You do not have permission to delete this license.'
        );
    }

    public function restore(Authenticatable $authenticatable, License $license): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['license.*.restore', "license.{$license->id}.restore"],
            denyResponse: 'You do not have permission to restore this license.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, License $license): Response
    {
        return $authenticatable->canOrElse(
            abilities: ['license.*.force-delete', "license.{$license->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this license.'
        );
    }
}

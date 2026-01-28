<?php

namespace App\Policies;

use App\Models\Authenticatable;
use App\Models\Export;
use Illuminate\Auth\Access\Response;

class ExportPolicy
{
    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'export_hub.view-any',
            denyResponse: 'You do not have permission to view exports.'
        );
    }

    public function view(Authenticatable $authenticatable, Export $export): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'export_hub.view',
            denyResponse: 'You do not have permission to view this export.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'export_hub.create',
            denyResponse: 'You do not have permission to create exports.'
        );
    }

    public function update(Authenticatable $authenticatable, Export $export): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'export_hub.update',
            denyResponse: 'You do not have permission to update this export.'
        );
    }

    public function delete(Authenticatable $authenticatable, Export $export): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'export_hub.delete',
            denyResponse: 'You do not have permission to delete this export.'
        );
    }

    public function restore(Authenticatable $authenticatable, Export $export): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'export_hub.restore',
            denyResponse: 'You do not have permission to restore this export.'
        );
    }

    public function import(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'export_hub.import',
            denyResponse: 'You do not have permission to import exports.'
        );
    }
}

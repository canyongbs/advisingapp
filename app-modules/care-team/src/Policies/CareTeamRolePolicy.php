<?php

namespace AdvisingApp\CareTeam\Policies;

use AdvisingApp\CareTeam\Models\CareTeamRole;
use App\Models\Authenticatable;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CareTeamRolePolicy
{
    public function viewAny(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'product_admin.view-any',
            denyResponse: 'You do not have permission to view care team roles.'
        );
    }

    public function view(Authenticatable $authenticatable, CareTeamRole $careTeamRole): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["product_admin.{$careTeamRole->getKey()}.view"],
            denyResponse: 'You do not have permission to view this care team role.'
        );
    }

    public function create(Authenticatable $authenticatable): Response
    {
        return $authenticatable->canOrElse(
            abilities: 'product_admin.create',
            denyResponse: 'You do not have permission to create care team roles.'
        );
    }

    public function update(Authenticatable $authenticatable, CareTeamRole $careTeamRole): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["product_admin.{$careTeamRole->getKey()}.update"],
            denyResponse: 'You do not have permission to update this care team role.'
        );
    }

    public function delete(Authenticatable $authenticatable, CareTeamRole $careTeamRole): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["product_admin.{$careTeamRole->getKey()}.delete"],
            denyResponse: 'You do not have permission to delete this care team role.'
        );
    }

    public function restore(Authenticatable $authenticatable, CareTeamRole $careTeamRole): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["product_admin.{$careTeamRole->getKey()}.restore"],
            denyResponse: 'You do not have permission to restore this care team role.'
        );
    }

    public function forceDelete(Authenticatable $authenticatable, CareTeamRole $careTeamRole): Response
    {
        return $authenticatable->canOrElse(
            abilities: ["product_admin.{$careTeamRole->getKey()}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this care team role.'
        );
    }
}

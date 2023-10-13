<?php

namespace Assist\Authorization\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Authorization\Models\RoleGroup;

class RoleGroupPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'role_group.view-any',
            denyResponse: 'You do not have permission to view role groups.'
        );
    }

    public function view(User $user, RoleGroup $roleGroup): Response
    {
        return $user->canOrElse(
            abilities: ['role_group.*.view', "role_group.{$roleGroup->id}.view"],
            denyResponse: 'You do not have permission to view this role group.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'role_group.create',
            denyResponse: 'You do not have permission to create role groups.'
        );
    }

    public function update(User $user, RoleGroup $roleGroup): Response
    {
        return $user->canOrElse(
            abilities: ['role_group.*.update', "role_group.{$roleGroup->id}.update"],
            denyResponse: 'You do not have permission to update this role group.'
        );
    }

    public function delete(User $user, RoleGroup $roleGroup): Response
    {
        return $user->canOrElse(
            abilities: ['role_group.*.delete', "role_group.{$roleGroup->id}.delete"],
            denyResponse: 'You do not have permission to delete this role group.'
        );
    }

    public function restore(User $user, RoleGroup $roleGroup): Response
    {
        return $user->canOrElse(
            abilities: ['role_group.*.restore', "role_group.{$roleGroup->id}.restore"],
            denyResponse: 'You do not have permission to restore this role group.'
        );
    }

    public function forceDelete(User $user, RoleGroup $roleGroup): Response
    {
        return $user->canOrElse(
            abilities: ['role_group.*.force-delete', "role_group.{$roleGroup->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this role group.'
        );
    }
}

<?php

namespace Assist\Authorization\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Authorization\Models\Role;

class RolePolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'role.view-any',
            denyResponse: 'You do not have permission to view roles.'
        );
    }

    public function view(User $user, Role $role): Response
    {
        return $user->canOrElse(
            abilities: ['role.*.view', "role.{$role->id}.view"],
            denyResponse: 'You do not have permission to view this role.'
        );
    }

    public function create(User $user): Response
    {
        return Response::deny('Roles cannot be created.');
    }

    public function update(User $user, Role $role): Response
    {
        return Response::deny('Permissions cannot be updated.');
    }

    public function delete(User $user, Role $role): Response
    {
        return Response::deny('Permissions cannot be deleted.');
    }

    public function restore(User $user, Role $role): Response
    {
        return Response::deny('Permissions cannot be restore.');
    }

    public function forceDelete(User $user, Role $role): Response
    {
        return Response::deny('Permissions cannot be force deleted.');
    }
}

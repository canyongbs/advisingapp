<?php

namespace Assist\Authorization\Policies;

use App\Models\User;
use Assist\Authorization\Models\Permission;
use Illuminate\Auth\Access\Response;

class PermissionPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'permission.view-any',
            denyResponse: 'You do not have permission to view permissions.'
        );
    }

    public function view(User $user, Permission $permission): Response
    {
        return $user->canOrElse(
            abilities: 'permission.*.view',
            denyResponse: 'You do not have permission to view this permission.'
        );
    }

    public function create(User $user): Response
    {
        return Response::deny('Permissions cannot be created.');
    }

    public function update(User $user, Permission $permission): Response
    {
        return Response::deny('Permissions cannot be updated.');
    }

    public function delete(User $user, Permission $permission): Response
    {
        return Response::deny('Permissions cannot be deleted.');
    }

    public function restore(User $user, Permission $permission): Response
    {
        return Response::deny('Permissions cannot be restored.');
    }

    public function forceDelete(User $user, Permission $permission): Response
    {
        return Response::deny('Permissions cannot be force deleted.');
    }
}

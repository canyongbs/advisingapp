<?php

namespace Assist\Division\Policies;

use App\Models\User;
use Assist\Division\Models\Division;
use Illuminate\Auth\Access\Response;

class DivisionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $user->can('division.view-any')
            ? Response::allow()
            : Response::deny('You do not have permission to view divisions.');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Division $division): Response
    {
        return $user->can('division.*.view') || $user->can("division.{$division->id}.view")
            ? Response::allow()
            : Response::deny('You do not have permission to view this division.');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->can('division.create')
            ? Response::allow()
            : Response::deny('You do not have permission to create divisions.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Division $division): Response
    {
        return $user->can('division.*.update') || $user->can("division.{$division->id}.update")
            ? Response::allow()
            : Response::deny('You do not have permission to update this division.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Division $division): Response
    {
        return $user->can('division.*.delete') || $user->can("division.{$division->id}.delete")
            ? Response::allow()
            : Response::deny('You do not have permission to delete this division.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Division $division): Response
    {
        return $user->can('division.*.restore') || $user->can("division.{$division->id}.restore")
            ? Response::allow()
            : Response::deny('You do not have permission to restore this division.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Division $division): Response
    {
        return $user->can('division.*.force-delete') || $user->can("division.{$division->id}.force-delete")
            ? Response::allow()
            : Response::deny('You do not have permission to permanently delete this division.');
    }
}

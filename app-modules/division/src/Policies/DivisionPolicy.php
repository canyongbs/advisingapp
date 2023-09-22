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
        return $user->canOrElse(
            'division.view-any',
            'You do not have permission to view divisions.'
        );
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Division $division): Response
    {
        return $user->canOrElse(
            ['division.*.view', "division.{$division->id}.view"],
            'You do not have permission to view this division.'
        );
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->canOrElse(
            'division.create',
            'You do not have permission to create divisions.'
        );
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Division $division): Response
    {
        return $user->canOrElse(
            ['division.*.update', "division.{$division->id}.update"],
            'You do not have permission to update this division.'
        );
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Division $division): Response
    {
        return $user->canOrElse(
            ['division.*.delete', "division.{$division->id}.delete"],
            'You do not have permission to delete this division.'
        );
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Division $division): Response
    {
        return $user->canOrElse(
            ['division.*.restore', "division.{$division->id}.restore"],
            'You do not have permission to restore this division.'
        );
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Division $division): Response
    {
        return $user->canOrElse(
            ['division.*.force-delete', "division.{$division->id}.force-delete"],
            'You do not have permission to permanently delete this division.'
        );
    }
}

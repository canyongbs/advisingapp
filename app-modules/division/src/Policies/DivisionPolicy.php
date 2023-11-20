<?php

namespace Assist\Division\Policies;

use App\Models\User;
use Assist\Division\Models\Division;
use Illuminate\Auth\Access\Response;

class DivisionPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'division.view-any',
            denyResponse: 'You do not have permission to view divisions.'
        );
    }

    public function view(User $user, Division $division): Response
    {
        return $user->canOrElse(
            abilities: ['division.*.view', "division.{$division->id}.view"],
            denyResponse: 'You do not have permission to view this division.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'division.create',
            denyResponse: 'You do not have permission to create divisions.'
        );
    }

    public function update(User $user, Division $division): Response
    {
        return $user->canOrElse(
            abilities: ['division.*.update', "division.{$division->id}.update"],
            denyResponse: 'You do not have permission to update this division.'
        );
    }

    public function delete(User $user, Division $division): Response
    {
        return $user->canOrElse(
            abilities: ['division.*.delete', "division.{$division->id}.delete"],
            denyResponse: 'You do not have permission to delete this division.'
        );
    }

    public function restore(User $user, Division $division): Response
    {
        return $user->canOrElse(
            abilities: ['division.*.restore', "division.{$division->id}.restore"],
            denyResponse: 'You do not have permission to restore this division.'
        );
    }

    public function forceDelete(User $user, Division $division): Response
    {
        return $user->canOrElse(
            abilities: ['division.*.force-delete', "division.{$division->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this division.'
        );
    }
}

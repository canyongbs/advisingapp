<?php

namespace Assist\AssistDataModel\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\AssistDataModel\Models\Performance;

class PerformancePolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'performance.view-any',
            denyResponse: 'You do not have permission to view performances.'
        );
    }

    public function view(User $user, Performance $performance): Response
    {
        return $user->canOrElse(
            abilities: ['performance.*.view', "performance.{$performance->id}.view"],
            denyResponse: 'You do not have permission to view this performance.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'performance.create',
            denyResponse: 'You do not have permission to create performances.'
        );
    }

    public function update(User $user, Performance $performance): Response
    {
        return $user->canOrElse(
            abilities: ['performance.*.update', "performance.{$performance->id}.update"],
            denyResponse: 'You do not have permission to update this performance.'
        );
    }

    public function delete(User $user, Performance $performance): Response
    {
        return $user->canOrElse(
            abilities: ['performance.*.delete', "performance.{$performance->id}.delete"],
            denyResponse: 'You do not have permission to delete this performance.'
        );
    }

    public function restore(User $user, Performance $performance): Response
    {
        return $user->canOrElse(
            abilities: ['performance.*.restore', "performance.{$performance->id}.restore"],
            denyResponse: 'You do not have permission to restore this performance.'
        );
    }

    public function forceDelete(User $user, Performance $performance): Response
    {
        return $user->canOrElse(
            abilities: ['performance.*.force-delete', "performance.{$performance->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this performance.'
        );
    }
}

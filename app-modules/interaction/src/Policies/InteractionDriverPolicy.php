<?php

namespace Assist\InteractionDriver\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Interaction\Models\InteractionDriver;

class InteractionDriverPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'interaction_driver.view-any',
            denyResponse: 'You do not have permission to view interaction drivers.'
        );
    }

    public function view(User $user, InteractionDriver $driver): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_driver.*.view', "interaction_driver.{$driver->id}.view"],
            denyResponse: 'You do not have permission to view this interaction driver.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'interaction_driver.create',
            denyResponse: 'You do not have permission to create interaction drivers.'
        );
    }

    public function update(User $user, InteractionDriver $driver): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_driver.*.update', "interaction_driver.{$driver->id}.update"],
            denyResponse: 'You do not have permission to update this interaction driver.'
        );
    }

    public function delete(User $user, InteractionDriver $driver): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_driver.*.delete', "interaction_driver.{$driver->id}.delete"],
            denyResponse: 'You do not have permission to delete this interaction driver.'
        );
    }

    public function restore(User $user, InteractionDriver $driver): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_driver.*.restore', "interaction_driver.{$driver->id}.restore"],
            denyResponse: 'You do not have permission to restore this interaction driver.'
        );
    }

    public function forceDelete(User $user, InteractionDriver $driver): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_driver.*.force-delete', "interaction_driver.{$driver->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this interaction driver.'
        );
    }
}

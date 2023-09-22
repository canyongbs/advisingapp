<?php

namespace Assist\Interaction\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Interaction\Models\Interaction;

class InteractionPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'interaction.view-any',
            denyResponse: 'You do not have permission to view interactions.'
        );
    }

    public function view(User $user, Interaction $interaction): Response
    {
        return $user->canOrElse(
            abilities: ['interaction.*.view', "interaction.{$interaction->id}.view"],
            denyResponse: 'You do not have permission to view this interaction.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'interaction.create',
            denyResponse: 'You do not have permission to create interactions.'
        );
    }

    public function update(User $user, Interaction $interaction): Response
    {
        return $user->canOrElse(
            abilities: ['interaction.*.update', "interaction.{$interaction->id}.update"],
            denyResponse: 'You do not have permission to update this interaction.'
        );
    }

    public function delete(User $user, Interaction $interaction): Response
    {
        return $user->canOrElse(
            abilities: ['interaction.*.delete', "interaction.{$interaction->id}.delete"],
            denyResponse: 'You do not have permission to delete this interaction.'
        );
    }

    public function restore(User $user, Interaction $interaction): Response
    {
        return $user->canOrElse(
            abilities: ['interaction.*.restore', "interaction.{$interaction->id}.restore"],
            denyResponse: 'You do not have permission to restore this interaction.'
        );
    }

    public function forceDelete(User $user, Interaction $interaction): Response
    {
        return $user->canOrElse(
            abilities: ['interaction.*.force-delete', "interaction.{$interaction->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this interaction.'
        );
    }
}

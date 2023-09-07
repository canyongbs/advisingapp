<?php

namespace Assist\Interaction\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Interaction\Models\InteractionOutcome;

class InteractionOutcomePolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'interaction_outcome.view-any',
            denyResponse: 'You do not have permission to view interaction outcomes.'
        );
    }

    public function view(User $user, InteractionOutcome $outcome): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_outcome.*.view', "interaction_outcome.{$outcome->id}.view"],
            denyResponse: 'You do not have permission to view this interaction outcome.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'interaction_outcome.create',
            denyResponse: 'You do not have permission to create interaction outcomes.'
        );
    }

    public function update(User $user, InteractionOutcome $outcome): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_outcome.*.update', "interaction_outcome.{$outcome->id}.update"],
            denyResponse: 'You do not have permission to update this interaction outcome.'
        );
    }

    public function delete(User $user, InteractionOutcome $outcome): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_outcome.*.delete', "interaction_outcome.{$outcome->id}.delete"],
            denyResponse: 'You do not have permission to delete this interaction outcome.'
        );
    }

    public function restore(User $user, InteractionOutcome $outcome): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_outcome.*.restore', "interaction_outcome.{$outcome->id}.restore"],
            denyResponse: 'You do not have permission to restore this interaction outcome.'
        );
    }

    public function forceDelete(User $user, InteractionOutcome $outcome): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_outcome.*.force-delete', "interaction_outcome.{$outcome->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this interaction outcome.'
        );
    }
}

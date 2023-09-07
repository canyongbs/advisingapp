<?php

namespace Assist\InteractionType\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Interaction\Models\InteractionType;

class InteractionTypePolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'interaction_type.view-any',
            denyResponse: 'You do not have permission to view interaction types.'
        );
    }

    public function view(User $user, InteractionType $type): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_type.*.view', "interaction_type.{$type->id}.view"],
            denyResponse: 'You do not have permission to view this interaction type.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'interaction_type.create',
            denyResponse: 'You do not have permission to create interaction types.'
        );
    }

    public function update(User $user, InteractionType $type): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_type.*.update', "interaction_type.{$type->id}.update"],
            denyResponse: 'You do not have permission to update this interaction type.'
        );
    }

    public function delete(User $user, InteractionType $type): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_type.*.delete', "interaction_type.{$type->id}.delete"],
            denyResponse: 'You do not have permission to delete this interaction type.'
        );
    }

    public function restore(User $user, InteractionType $type): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_type.*.restore', "interaction_type.{$type->id}.restore"],
            denyResponse: 'You do not have permission to restore this interaction type.'
        );
    }

    public function forceDelete(User $user, InteractionType $type): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_type.*.force-delete', "interaction_type.{$type->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this interaction type.'
        );
    }
}

<?php

namespace Assist\Interaction\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Interaction\Models\InteractionRelation;

class InteractionRelationPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'interaction_relation.view-any',
            denyResponse: 'You do not have permission to view interaction relations.'
        );
    }

    public function view(User $user, InteractionRelation $relation): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_relation.*.view', "interaction_relation.{$relation->id}.view"],
            denyResponse: 'You do not have permission to view this interaction relation.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'interaction_relation.create',
            denyResponse: 'You do not have permission to create interaction relations.'
        );
    }

    public function update(User $user, InteractionRelation $relation): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_relation.*.update', "interaction_relation.{$relation->id}.update"],
            denyResponse: 'You do not have permission to update this interaction relation.'
        );
    }

    public function delete(User $user, InteractionRelation $relation): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_relation.*.delete', "interaction_relation.{$relation->id}.delete"],
            denyResponse: 'You do not have permission to delete this interaction relation.'
        );
    }

    public function restore(User $user, InteractionRelation $relation): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_relation.*.restore', "interaction_relation.{$relation->id}.restore"],
            denyResponse: 'You do not have permission to restore this interaction relation.'
        );
    }

    public function forceDelete(User $user, InteractionRelation $relation): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_relation.*.force-delete', "interaction_relation.{$relation->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this interaction relation.'
        );
    }
}

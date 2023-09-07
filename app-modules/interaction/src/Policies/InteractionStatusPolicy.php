<?php

namespace Assist\Interaction\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Interaction\Models\InteractionStatus;

class InteractionStatusPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'interaction_status.view-any',
            denyResponse: 'You do not have permission to view interaction statuses.'
        );
    }

    public function view(User $user, InteractionStatus $status): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_status.*.view', "interaction_status.{$status->id}.view"],
            denyResponse: 'You do not have permission to view this interaction status.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'interaction_status.create',
            denyResponse: 'You do not have permission to create interaction statuses.'
        );
    }

    public function update(User $user, InteractionStatus $status): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_status.*.update', "interaction_status.{$status->id}.update"],
            denyResponse: 'You do not have permission to update this interaction status.'
        );
    }

    public function delete(User $user, InteractionStatus $status): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_status.*.delete', "interaction_status.{$status->id}.delete"],
            denyResponse: 'You do not have permission to delete this interaction status.'
        );
    }

    public function restore(User $user, InteractionStatus $status): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_status.*.restore', "interaction_status.{$status->id}.restore"],
            denyResponse: 'You do not have permission to restore this interaction status.'
        );
    }

    public function forceDelete(User $user, InteractionStatus $status): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_status.*.force-delete', "interaction_status.{$status->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this interaction status.'
        );
    }
}

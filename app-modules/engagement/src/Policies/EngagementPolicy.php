<?php

namespace Assist\Engagement\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Engagement\Models\Engagement;

class EngagementPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'engagement.view-any',
            denyResponse: 'You do not have permission to view engagements.'
        );
    }

    public function view(User $user, Engagement $engagement): Response
    {
        return $user->canOrElse(
            abilities: ['engagement.*.view', "engagement.{$engagement->id}.view"],
            denyResponse: 'You do not have permission to view this engagement.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'engagement.create',
            denyResponse: 'You do not have permission to create engagements.'
        );
    }

    public function update(User $user, Engagement $engagement): Response
    {
        if ($engagement->hasBeenDelivered()) {
            return Response::deny('You do not have permission to update this engagement because it has already been delivered.');
        }

        return $user->canOrElse(
            abilities: ['engagement.*.update', "engagement.{$engagement->id}.update"],
            denyResponse: 'You do not have permission to update this engagement.'
        );
    }

    public function delete(User $user, Engagement $engagement): Response
    {
        if ($engagement->hasBeenDelivered()) {
            return Response::deny('You do not have permission to delete this engagement because it has already been delivered.');
        }

        return $user->canOrElse(
            abilities: ['engagement.*.delete', "engagement.{$engagement->id}.delete"],
            denyResponse: 'You do not have permission to delete this engagement.'
        );
    }

    public function restore(User $user, Engagement $engagement): Response
    {
        return $user->canOrElse(
            abilities: ['engagement.*.restore', "engagement.{$engagement->id}.restore"],
            denyResponse: 'You do not have permission to restore this engagement.'
        );
    }

    public function forceDelete(User $user, Engagement $engagement): Response
    {
        if ($engagement->hasBeenDelivered()) {
            return Response::deny('You cannot permanently delete this engagement because it has already been delivered.');
        }

        return $user->canOrElse(
            abilities: ['engagement.*.force-delete', "engagement.{$engagement->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this engagement.'
        );
    }
}

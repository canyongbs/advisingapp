<?php

namespace Assist\Engagement\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Engagement\Models\EngagementDeliverable;

class EngagementDeliverablePolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'engagement_deliverable.view-any',
            denyResponse: 'You do not have permission to view engagements.'
        );
    }

    public function view(User $user, EngagementDeliverable $deliverable): Response
    {
        return $user->canOrElse(
            abilities: ['engagement_deliverable.*.view', "engagement_deliverable.{$deliverable->id}.view"],
            denyResponse: 'You do not have permission to view this engagement deliverable.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: ['engagement_deliverable.create'],
            denyResponse: 'You do not have permission to create engagement deliverables.'
        );
    }

    public function update(User $user, EngagementDeliverable $deliverable): Response
    {
        if ($deliverable->hasBeenDelivered()) {
            return Response::deny('You do not have permission to update this engagement deliverable because it has already been sent.');
        }

        return $user->canOrElse(
            abilities: ['engagement_deliverable.*.update', "engagement_deliverable.{$deliverable->id}.update"],
            denyResponse: 'You do not have permission to update this engagement deliverable.'
        );
    }

    public function delete(User $user, EngagementDeliverable $deliverable): Response
    {
        if ($deliverable->hasBeenDelivered()) {
            return Response::deny('You do not have permission to delete this engagement deliverable because it has already been sent.');
        }

        return $user->canOrElse(
            abilities: ['engagement_deliverable.*.delete', "engagement_deliverable.{$deliverable->id}.delete"],
            denyResponse: 'You do not have permission to delete this engagement deliverable.'
        );
    }

    public function restore(User $user, EngagementDeliverable $deliverable): Response
    {
        return $user->canOrElse(
            abilities: ['engagement_deliverable.*.restore', "engagement_deliverable.{$deliverable->id}.restore"],
            denyResponse: 'You do not have permission to restore this engagement deliverable.'
        );
    }

    public function forceDelete(User $user, EngagementDeliverable $deliverable): Response
    {
        if ($deliverable->hasBeenDelivered()) {
            return Response::deny('You cannot permanently delete this engagement deliverable because it has already been delivered.');
        }

        return $user->canOrElse(
            abilities: ['engagement_deliverable.*.force-delete', "engagement_deliverable.{$deliverable->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this engagement deliverable.'
        );
    }
}

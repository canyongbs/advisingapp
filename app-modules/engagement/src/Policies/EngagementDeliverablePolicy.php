<?php

namespace Assist\Engagement\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Engagement\Models\EngagementDeliverable;

class EngagementDeliverablePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $user->can('engagement_deliverable.view-any')
            ? Response::allow()
            : Response::deny('You do not have permission to view engagements.');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EngagementDeliverable $deliverable): Response
    {
        return $user->can('engagement_deliverable.*.view') || $user->can("engagement_deliverable.{$deliverable->id}.view")
            ? Response::allow()
            : Response::deny('You do not have permission to view this engagement deliverable.');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->can('engagement_deliverable.create')
            ? Response::allow()
            : Response::deny('You do not have permission to create engagement deliverables.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EngagementDeliverable $deliverable): Response
    {
        if ($deliverable->hasBeenDelivered()) {
            return Response::deny('You do not have permission to update this engagement deliverable because it has already been sent.');
        }

        return $user->can('engagement_deliverable.*.update') || $user->can("engagement_deliverable.{$deliverable->id}.update")
            ? Response::allow()
            : Response::deny('You do not have permission to update this engagement deliverable.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EngagementDeliverable $deliverable): Response
    {
        if ($deliverable->hasBeenDelivered()) {
            return Response::deny('You do not have permission to delete this engagement deliverable because it has already been sent.');
        }

        return $user->can('engagement_deliverable.*.delete') || $user->can("engagement_deliverable.{$deliverable->id}.delete")
            ? Response::allow()
            : Response::deny('You do not have permission to delete this engagement deliverable.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, EngagementDeliverable $deliverable): Response
    {
        return $user->can('engagement_deliverable.*.restore') || $user->can("engagement_deliverable.{$deliverable->id}.restore")
            ? Response::allow()
            : Response::deny('You do not have permission to restore this engagement deliverable.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, EngagementDeliverable $deliverable): Response
    {
        if ($deliverable->hasBeenDelivered()) {
            return Response::deny('You cannot permanently delete this engagement deliverable because it has already been delivered.');
        }

        return $user->can('engagement_deliverable.*.force-delete') || $user->can("engagement_deliverable.{$deliverable->id}.force-delete")
            ? Response::allow()
            : Response::deny('You do not have permission to permanently delete this engagement deliverable.');
    }
}

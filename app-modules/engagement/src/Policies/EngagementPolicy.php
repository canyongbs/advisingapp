<?php

namespace Assist\Engagement\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Engagement\Models\Engagement;

class EngagementPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $user->can('engagement.view-any')
            ? Response::allow()
            : Response::deny('You do not have permission to view engagements.');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Engagement $engagement): Response
    {
        return $user->can('engagement.*.view') || $user->can("engagement.{$engagement->id}.view")
            ? Response::allow()
            : Response::deny('You do not have permission to view this engagement.');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->can('engagement.create')
            ? Response::allow()
            : Response::deny('You do not have permission to create engagements.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Engagement $engagement): Response
    {
        if ($engagement->hasBeenDelivered()) {
            return Response::deny('You do not have permission to update this engagement because it has already been delivered.');
        }

        return $user->can('engagement.*.update') || $user->can("engagement.{$engagement->id}.update")
            ? Response::allow()
            : Response::deny('You do not have permission to update this engagement.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Engagement $engagement): Response
    {
        if ($engagement->hasBeenDelivered()) {
            return Response::deny('You do not have permission to delete this engagement because it has already been delivered.');
        }

        return $user->can('engagement.*.delete') || $user->can("engagement.{$engagement->id}.delete")
            ? Response::allow()
            : Response::deny('You do not have permission to delete this engagement.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Engagement $engagement): Response
    {
        return $user->can('engagement.*.restore') || $user->can("engagement.{$engagement->id}.restore")
            ? Response::allow()
            : Response::deny('You do not have permission to restore this engagement.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Engagement $engagement): Response
    {
        if ($engagement->hasBeenDelivered()) {
            return Response::deny('You cannot permanently delete this engagement because it has already been delivered.');
        }

        return $user->can('engagement.*.force-delete') || $user->can("engagement.{$engagement->id}.force-delete")
            ? Response::allow()
            : Response::deny('You do not have permission to permanently delete this engagement.');
    }
}

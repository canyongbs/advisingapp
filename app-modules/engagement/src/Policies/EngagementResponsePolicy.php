<?php

namespace Assist\Engagement\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Engagement\Models\EngagementResponse;

class EngagementResponsePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $user->can('engagement_response.view-any')
            ? Response::allow()
            : Response::deny('You do not have permission to view engagement responses.');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EngagementResponse $engagementResponse): Response
    {
        return $user->can('engagement_response.*.view') || $user->can("engagement_response.{$engagementResponse->id}.view")
            ? Response::allow()
            : Response::deny('You do not have permission to view this engagement response.');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->can('engagement_response.create')
            ? Response::allow()
            : Response::deny('You do not have permission to create engagement responses.');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EngagementResponse $engagementResponse): Response
    {
        return $user->can('engagement_response.*.update') || $user->can("engagement_response.{$engagementResponse->id}.update")
            ? Response::allow()
            : Response::deny('You do not have permission to update this engagement response.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EngagementResponse $engagementResponse): Response
    {
        return $user->can('engagement_response.*.delete') || $user->can("engagement_response.{$engagement->id}.delete")
            ? Response::allow()
            : Response::deny('You do not have permission to delete this engagement response.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, EngagementResponse $engagementResponse): Response
    {
        return $user->can('engagement_response.*.restore') || $user->can("engagement_response.{$engagementResponse->id}.restore")
            ? Response::allow()
            : Response::deny('You do not have permission to restore this engagement response.');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, EngagementResponse $engagementResponse): Response
    {
        return $user->can('engagement_response.*.force-delete') || $user->can("engagement_response.{$engagementResponse->id}.force-delete")
            ? Response::allow()
            : Response::deny('You do not have permission to permanently delete this engagement response.');
    }
}

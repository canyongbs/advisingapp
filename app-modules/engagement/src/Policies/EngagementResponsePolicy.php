<?php

namespace Assist\Engagement\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Engagement\Models\EngagementResponse;

class EngagementResponsePolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'engagement_response.view-any',
            denyResponse: 'You do not have permission to view engagement responses.'
        );
    }

    public function view(User $user, EngagementResponse $engagementResponse): Response
    {
        return $user->canOrElse(
            abilities: ['engagement_response.*.view', "engagement_response.{$engagementResponse->id}.view"],
            denyResponse: 'You do not have permission to view this engagement response.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'engagement_response.create',
            denyResponse: 'You do not have permission to create engagement responses.'
        );
    }

    public function update(User $user, EngagementResponse $engagementResponse): Response
    {
        return $user->canOrElse(
            abilities: ['engagement_response.*.update', "engagement_response.{$engagementResponse->id}.update"],
            denyResponse: 'You do not have permission to update this engagement response.'
        );
    }

    public function delete(User $user, EngagementResponse $engagementResponse): Response
    {
        return $user->canOrElse(
            abilities: ['engagement_response.*.delete', "engagement_response.{$engagementResponse->id}.delete"],
            denyResponse: 'You do not have permission to delete this engagement response.'
        );
    }

    public function restore(User $user, EngagementResponse $engagementResponse): Response
    {
        return $user->canOrElse(
            abilities: ['engagement_response.*.restore', "engagement_response.{$engagementResponse->id}.restore"],
            denyResponse: 'You do not have permission to restore this engagement response.'
        );
    }

    public function forceDelete(User $user, EngagementResponse $engagementResponse): Response
    {
        return $user->canOrElse(
            abilities: ['engagement_response.*.force-delete', "engagement_response.{$engagementResponse->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this engagement response.'
        );
    }
}

<?php

namespace Assist\Engagement\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Engagement\Models\EngagementFile;

class EngagementFilePolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'engagement_file.view-any',
            denyResponse: 'You do not have permissions to view engagement files.'
        );
    }

    public function view(User $user, EngagementFile $engagementFile): Response
    {
        return $user->canOrElse(
            abilities: ['engagement_file.*.view', "engagement_file.{$engagementFile->id}.view"],
            denyResponse: 'You do not have permissions to view this engagement file.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'engagement_file.create',
            denyResponse: 'You do not have permissions to create engagement files.'
        );
    }

    public function update(User $user, EngagementFile $engagementFile): Response
    {
        return $user->canOrElse(
            abilities: ['engagement_file.*.update', "engagement_file.{$engagementFile->id}.update"],
            denyResponse: 'You do not have permissions to update this engagement file.'
        );
    }

    public function delete(User $user, EngagementFile $engagementFile): Response
    {
        return $user->canOrElse(
            abilities: ['engagement_file.*.delete', "engagement_file.{$engagementFile->id}.delete"],
            denyResponse: 'You do not have permissions to delete this engagement file.'
        );
    }

    public function restore(User $user, EngagementFile $engagementFile): Response
    {
        return $user->canOrElse(
            abilities: ['engagement_file.*.restore', "engagement_file.{$engagementFile->id}.restore"],
            denyResponse: 'You do not have permissions to restore this engagement file.'
        );
    }

    public function forceDelete(User $user, EngagementFile $engagementFile): Response
    {
        return $user->canOrElse(
            abilities: ['engagement_file.*.force-delete', "engagement_file.{$engagementFile->id}.force-delete"],
            denyResponse: 'You do not have permissions to force delete this engagement file.'
        );
    }
}

<?php

namespace Assist\Engagement\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Engagement\Models\EngagementFile;

class EngagementFilePolicy
{
    public function viewAny(User $user): Response
    {
        return $user->can('engagement_file.view-any')
            ? Response::allow()
            : Response::deny('You do not have permissions to view engagement files.');
    }

    public function view(User $user, EngagementFile $engagementFile): Response
    {
        return $user->can('engagement_file.*.view') || $user->can("engagement_file.{$engagementFile->id}.view")
            ? Response::allow()
            : Response::deny('You do not have permissions to view this engagement file.');
    }

    public function create(User $user): Response
    {
        return $user->can('engagement_file.create')
            ? Response::allow()
            : Response::deny('You do not have permissions to create engagement files.');
    }

    public function update(User $user, EngagementFile $engagementFile): Response
    {
        return $user->can('engagement_file.*.update') || $user->can("engagement_file.{$engagementFile->id}.update")
            ? Response::allow()
            : Response::deny('You do not have permissions to update this engagement file.');
    }

    public function delete(User $user, EngagementFile $engagementFile): Response
    {
        return $user->can('engagement_file.*.delete') || $user->can("engagement_file.{$engagementFile->id}.delete")
            ? Response::allow()
            : Response::deny('You do not have permissions to delete this engagement file.');
    }

    public function restore(User $user, EngagementFile $engagementFile): Response
    {
        return $user->can('engagement_file.*.restore') || $user->can("engagement_file.{$engagementFile->id}.restore")
            ? Response::allow()
            : Response::deny('You do not have permissions to restore this engagement file.');
    }

    public function forceDelete(User $user, EngagementFile $engagementFile): Response
    {
        return $user->can('engagement_file.*.force-delete') || $user->can("engagement_file.{$engagementFile->id}.force-delete")
            ? Response::allow()
            : Response::deny('You do not have permissions to force delete this engagement file.');
    }
}

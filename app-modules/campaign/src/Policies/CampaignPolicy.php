<?php

namespace Assist\Campaign\Policies;

use App\Models\User;
use Assist\Campaign\Models\Campaign;
use Illuminate\Auth\Access\Response;

class CampaignPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'campaign.view-any',
            denyResponse: 'You do not have permission to view campaigns.'
        );
    }

    public function view(User $user, Campaign $campaign): Response
    {
        return $user->canOrElse(
            abilities: ['campaign.*.view', "campaign.{$campaign->id}.view"],
            denyResponse: 'You do not have permission to view this campaign.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'campaign.create',
            denyResponse: 'You do not have permission to create campaigns.'
        );
    }

    public function update(User $user, Campaign $campaign): Response
    {
        return $user->canOrElse(
            abilities: ['campaign.*.update', "campaign.{$campaign->id}.update"],
            denyResponse: 'You do not have permission to update this campaign.'
        );
    }

    public function delete(User $user, Campaign $campaign): Response
    {
        return $user->canOrElse(
            abilities: ['campaign.*.delete', "campaign.{$campaign->id}.delete"],
            denyResponse: 'You do not have permission to delete this campaign.'
        );
    }

    public function restore(User $user, Campaign $campaign): Response
    {
        return $user->canOrElse(
            abilities: ['campaign.*.restore', "campaign.{$campaign->id}.restore"],
            denyResponse: 'You do not have permission to restore this campaign.'
        );
    }

    public function forceDelete(User $user, Campaign $campaign): Response
    {
        return $user->canOrElse(
            abilities: ['campaign.*.force-delete', "campaign.{$campaign->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this campaign.'
        );
    }
}

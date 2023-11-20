<?php

namespace Assist\Campaign\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Campaign\Models\CampaignAction;

class CampaignActionPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'campaign_action.view-any',
            denyResponse: 'You do not have permission to view campaign actions.'
        );
    }

    public function view(User $user, CampaignAction $campaignAction): Response
    {
        return $user->canOrElse(
            abilities: ['campaign_action.*.view', "campaign_action.{$campaignAction->id}.view"],
            denyResponse: 'You do not have permission to view this campaign action.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'campaign_action.create',
            denyResponse: 'You do not have permission to create campaign actions.'
        );
    }

    public function update(User $user, CampaignAction $campaignAction): Response
    {
        return $user->canOrElse(
            abilities: ['campaign_action.*.update', "campaign_action.{$campaignAction->id}.update"],
            denyResponse: 'You do not have permission to update this campaign action.'
        );
    }

    public function delete(User $user, CampaignAction $campaignAction): Response
    {
        return $user->canOrElse(
            abilities: ['campaign_action.*.delete', "campaign_action.{$campaignAction->id}.delete"],
            denyResponse: 'You do not have permission to delete this campaign action.'
        );
    }

    public function restore(User $user, CampaignAction $campaignAction): Response
    {
        return $user->canOrElse(
            abilities: ['campaign_action.*.restore', "campaign_action.{$campaignAction->id}.restore"],
            denyResponse: 'You do not have permission to restore this campaign action.'
        );
    }

    public function forceDelete(User $user, CampaignAction $campaignAction): Response
    {
        return $user->canOrElse(
            abilities: ['campaign_action.*.force-delete', "campaign_action.{$campaignAction->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this campaign action.'
        );
    }
}

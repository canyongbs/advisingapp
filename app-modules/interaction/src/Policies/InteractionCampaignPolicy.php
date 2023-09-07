<?php

namespace Assist\InteractionCampaign\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Assist\Interaction\Models\InteractionCampaign;

class InteractionCampaignPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'interaction_campaign.view-any',
            denyResponse: 'You do not have permission to view interaction campaigns.'
        );
    }

    public function view(User $user, InteractionCampaign $campaign): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_campaign.*.view', "interaction_campaign.{$campaign->id}.view"],
            denyResponse: 'You do not have permission to view this interaction campaign.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'interaction_campaign.create',
            denyResponse: 'You do not have permission to create interaction campaigns.'
        );
    }

    public function update(User $user, InteractionCampaign $campaign): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_campaign.*.update', "interaction_campaign.{$campaign->id}.update"],
            denyResponse: 'You do not have permission to update this interaction campaign.'
        );
    }

    public function delete(User $user, InteractionCampaign $campaign): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_campaign.*.delete', "interaction_campaign.{$campaign->id}.delete"],
            denyResponse: 'You do not have permission to delete this interaction campaign.'
        );
    }

    public function restore(User $user, InteractionCampaign $campaign): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_campaign.*.restore', "interaction_campaign.{$campaign->id}.restore"],
            denyResponse: 'You do not have permission to restore this interaction campaign.'
        );
    }

    public function forceDelete(User $user, InteractionCampaign $campaign): Response
    {
        return $user->canOrElse(
            abilities: ['interaction_campaign.*.force-delete', "interaction_campaign.{$campaign->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this interaction campaign.'
        );
    }
}

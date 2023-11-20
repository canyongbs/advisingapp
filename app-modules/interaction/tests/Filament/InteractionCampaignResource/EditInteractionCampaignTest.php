<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Interaction\Models\InteractionCampaign;
use Assist\Interaction\Filament\Resources\InteractionCampaignResource;

test('EditInteractionCampaign is gated with proper access control', function () {
    $user = User::factory()->create();

    $campaign = InteractionCampaign::factory()->create();

    actingAs($user)
        ->get(
            InteractionCampaignResource::getUrl('edit', ['record' => $campaign])
        )->assertForbidden();

    $user->givePermissionTo('interaction_campaign.view-any');
    $user->givePermissionTo('interaction_campaign.*.update');

    actingAs($user)
        ->get(
            InteractionCampaignResource::getUrl('edit', ['record' => $campaign])
        )->assertSuccessful();
});

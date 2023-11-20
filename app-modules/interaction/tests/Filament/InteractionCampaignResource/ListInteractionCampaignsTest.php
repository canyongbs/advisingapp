<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Interaction\Filament\Resources\InteractionCampaignResource;

test('ListInteractionCampaigns is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            InteractionCampaignResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('interaction_campaign.view-any');

    actingAs($user)
        ->get(
            InteractionCampaignResource::getUrl('index')
        )->assertSuccessful();
});

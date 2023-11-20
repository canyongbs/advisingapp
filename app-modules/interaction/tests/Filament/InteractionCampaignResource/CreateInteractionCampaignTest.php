<?php

use App\Models\User;

use function Pest\Laravel\actingAs;

use Assist\Interaction\Filament\Resources\InteractionCampaignResource;

test('CreateInteractionCampaign is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            InteractionCampaignResource::getUrl('create')
        )->assertForbidden();

    $user->givePermissionTo('interaction_campaign.view-any');
    $user->givePermissionTo('interaction_campaign.create');

    actingAs($user)
        ->get(
            InteractionCampaignResource::getUrl('create')
        )->assertSuccessful();
});

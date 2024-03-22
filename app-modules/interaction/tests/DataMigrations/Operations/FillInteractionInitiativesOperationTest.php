<?php

use function Pest\Laravel\artisan;

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\Interaction\Models\InteractionCampaign;
use AdvisingApp\Interaction\Models\InteractionInitiative;

it('will create Initiatives from all existing Campaigns', function () {
    InteractionCampaign::factory()->count(10)->createQuietly();

    expect(InteractionInitiative::count())->toBe(0);

    artisan('operations:process tenant 2024_03_19_123456_fill_interaction_initiatives --test --sync');

    expect(InteractionInitiative::count())->toBe(10);
});

it('Initiatives will be connected to the Interactions that the Campaigns were connected to', function () {
    $campaigns = InteractionCampaign::factory()->count(10)->createQuietly();

    $campaigns->each(function (InteractionCampaign $campaign) {
        $prospect = Prospect::factory()->create();

        Interaction::factory()->createQuietly([
            'interactable_id' => $prospect->id,
            'interactable_type' => $prospect->getMorphClass(),
            'interaction_campaign_id' => $campaign->id,
        ]);
    });

    artisan('operations:process tenant 2024_03_19_123456_fill_interaction_initiatives --test --sync');

    Interaction::cursor()->each(function (Interaction $interaction) {
        if (is_null($interaction->interaction_campaign_id)) {
            return;
        }

        $initiative = InteractionInitiative::where('name', $interaction->campaign->name)->first();

        expect($interaction->interaction_initiative_id)->toBe($initiative->id);
    });
});

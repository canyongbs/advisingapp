<?php

use function Pest\Laravel\artisan;

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
        Interaction::factory()->createQuietly([
            'interaction_campaign_id' => $campaign->id,
        ]);
    });

    artisan('operations:process tenant 2024_03_19_123456_fill_interaction_initiatives --test --sync');

    $campaigns->each(function (InteractionCampaign $campaign) {
        $initiative = InteractionInitiative::where('name', $campaign->name)->first();

        expect($campaign->interactions->first()->interaction_initiative_id)->toBe($initiative->id);
    });
});

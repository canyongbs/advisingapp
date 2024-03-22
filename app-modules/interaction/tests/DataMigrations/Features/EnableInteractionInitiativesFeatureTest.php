<?php

use Laravel\Pennant\Feature;

use function Tests\asSuperAdmin;
use function Pest\Livewire\livewire;

use AdvisingApp\Interaction\Models\Interaction;
use App\Features\EnableInteractionInitiativesFeature;
use AdvisingApp\Interaction\Models\InteractionCampaign;
use AdvisingApp\Interaction\Models\InteractionInitiative;
use AdvisingApp\Interaction\Filament\Resources\InteractionResource;
use AdvisingApp\Interaction\Filament\Resources\InteractionResource\Pages\CreateInteraction;

it('will create InteractionInitiatives when InteractionCampaigns are created', function () {
    expect(InteractionInitiative::count())->toBe(0);

    InteractionCampaign::factory()->create();

    expect(InteractionInitiative::count())->toBe(1);
});

it('will find and associate the Initiative that was created as a mirror of a Campaign when creating an Interaction with a Campaign', function () {
    // Given we have a Campaign
    $campaign = InteractionCampaign::factory()->create();

    // And we create an Interaction with that Campaign
    $interaction = Interaction::factory()->create([
        'interaction_campaign_id' => $campaign->id,
    ]);

    // Then we expect the Interaction to be related to the Initiative that was created as a mirror of the Campaign
    $initiative = InteractionInitiative::where('name', $campaign->name)->first();
    expect($interaction->interaction_initiative_id)->toBe($initiative->id);
});

it('will render the InteractionInitiative select after the feature has been activated', function () {
    asSuperAdmin()
        ->get(
            InteractionResource::getUrl('create')
        )
        ->assertSuccessful();

    livewire(CreateInteraction::class)
        ->assertFormFieldExists('interaction_campaign_id');

    Feature::activate(EnableInteractionInitiativesFeature::class);

    livewire(CreateInteraction::class)
        ->assertFormFieldExists('interaction_initiative_id');
});

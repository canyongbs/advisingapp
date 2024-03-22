<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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

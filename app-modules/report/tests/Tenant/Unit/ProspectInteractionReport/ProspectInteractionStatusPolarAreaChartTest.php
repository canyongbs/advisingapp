<?php

use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\Interaction\Models\InteractionStatus;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Report\Filament\Widgets\ProspectInteractionStatusPolarAreaChart;

it('checks prospect interaction status polar area chart', function () {
    $interactionsCount = rand(1, 10);

    $interactionStatusFirst = InteractionStatus::factory()->create();
    $interactionStatusSecond = InteractionStatus::factory()->create();
    $interactionStatusThird = InteractionStatus::factory()->create();

    Prospect::factory()->has(Interaction::factory()->count($interactionsCount)->for($interactionStatusFirst, 'status'), 'interactions')->create();
    Prospect::factory()->has(Interaction::factory()->count($interactionsCount)->for($interactionStatusSecond, 'status'), 'interactions')->create();
    Prospect::factory()->has(Interaction::factory()->count($interactionsCount)->for($interactionStatusThird, 'status'), 'interactions')->create();

    $widgetInstance = new ProspectInteractionStatusPolarAreaChart();
    $widgetInstance->cacheTag = 'report-prospect-interaction';

    $stats = $widgetInstance->getData()['datasets'][0]['data'];

    expect($interactionsCount)->toEqual($stats[0])
        ->and($interactionsCount)->toEqual($stats[1])
        ->and($interactionsCount)->toEqual($stats[2]);
});

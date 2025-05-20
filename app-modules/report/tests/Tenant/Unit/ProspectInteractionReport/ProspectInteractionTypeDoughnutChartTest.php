<?php

use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\Interaction\Models\InteractionType;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Report\Filament\Widgets\ProspectInteractionTypeDoughnutChart;

it('checks prospect interaction types doughnut chart', function () {
    $interactionsCount = rand(1, 10);

    $interactionTypeFirst = InteractionType::factory()->create();
    $interactionTypeSecond = InteractionType::factory()->create();
    $interactionTypeThird = InteractionType::factory()->create();

    Prospect::factory()->has(Interaction::factory()->count($interactionsCount)->for($interactionTypeFirst, 'type'), 'interactions')->create();
    Prospect::factory()->has(Interaction::factory()->count($interactionsCount)->for($interactionTypeSecond, 'type'), 'interactions')->create();
    Prospect::factory()->has(Interaction::factory()->count($interactionsCount)->for($interactionTypeThird, 'type'), 'interactions')->create();

    $widgetInstance = new ProspectInteractionTypeDoughnutChart();
    $widgetInstance->cacheTag = 'report-prospect-interaction';

    $stats = $widgetInstance->getData()['datasets'][0]['data'];

    expect($interactionsCount)->toEqual($stats[0])
        ->and($interactionsCount)->toEqual($stats[1])
        ->and($interactionsCount)->toEqual($stats[2]);
});

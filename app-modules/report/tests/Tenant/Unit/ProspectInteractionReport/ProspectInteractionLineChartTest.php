<?php

use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Report\Filament\Widgets\ProspectInteractionLineChart;

it('checks prospect interactions monthly line chart', function () {
    $prospectCount = 5;

    Prospect::factory()->count($prospectCount)->has(Interaction::factory()->count(5)->state([
        'created_at' => now()->subMonths(1),
    ]), 'interactions')->create();
    Prospect::factory()->count($prospectCount)->has(Interaction::factory()->count(5)->state([
        'created_at' => now()->subMonths(6),
    ]), 'interactions')->create();

    $widgetInstance = new ProspectInteractionLineChart();
    $widgetInstance->cacheTag = 'report-prospect-interaction';

    expect($widgetInstance->getData())->toMatchSnapshot();
});

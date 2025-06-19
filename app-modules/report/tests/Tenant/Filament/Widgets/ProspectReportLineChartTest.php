<?php

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Report\Filament\Widgets\ProspectReportLineChart;

it('returns correct cumulative prospect counts grouped by month within the given date range', function () {
    $startDate = now()->subDays(90);
    $endDate = now()->subDays(5);

    Prospect::factory()->count(5)->state([
        'created_at' => $startDate,
    ])->create();

    Prospect::factory()->count(5)->state([
        'created_at' => $endDate,
    ])->create();

    $widgetInstance = new ProspectReportLineChart();
    $widgetInstance->cacheTag = 'prospect-report-cache';
    $widgetInstance->filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    expect($widgetInstance->getData())->toMatchSnapshot();
});

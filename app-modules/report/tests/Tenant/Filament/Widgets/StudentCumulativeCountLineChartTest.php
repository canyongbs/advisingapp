<?php

use AdvisingApp\Report\Filament\Widgets\StudentCumulativeCountLineChart;
use AdvisingApp\StudentDataModel\Models\Student;

it('returns correct cumulative student counts grouped by month within the given date range', function () {
    $startDate = now()->subDays(90);
    $endDate = now()->subDays(5);

    Student::factory()->count(5)->state([
        'created_at_source' => $startDate,
    ])->create();

    Student::factory()->count(5)->state([
        'created_at_source' => $endDate,
    ])->create();

    $widgetInstance = new StudentCumulativeCountLineChart();
    $widgetInstance->cacheTag = 'report-student';
    $widgetInstance->filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    expect($widgetInstance->getData())->toMatchSnapshot();
});

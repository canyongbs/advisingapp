<?php

use AdvisingApp\Ai\Models\QnaAdvisorMessage;
use AdvisingApp\Report\Filament\Widgets\QnaAdvisorReportLineChart;

it('returns correct QnaAdvisorMessage counts grouped by month within the given date range', function () {
    $startDate = now()->subDays(90);
    $endDate = now()->subDays(5);

    QnaAdvisorMessage::factory()->count(5)->state([
        'created_at' => $startDate,
        'is_advisor' => false,
    ])->create();

    QnaAdvisorMessage::factory()->count(5)->state([
        'created_at' => $endDate,
        'is_advisor' => false,
    ])->create();

    $widgetInstance = new QnaAdvisorReportLineChart();
    $widgetInstance->cacheTag = 'qna-advisor-report-cache';
    $widgetInstance->filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    expect($widgetInstance->getData())->toMatchSnapshot();
});

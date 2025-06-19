<?php

use AdvisingApp\Alert\Models\Alert;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Report\Filament\Widgets\ProspectReportStats;
use AdvisingApp\Segment\Enums\SegmentModel;
use AdvisingApp\Segment\Models\Segment;
use AdvisingApp\Task\Models\Task;

it('returns correct total prospect stats of prospects, alerts, segments and tasks within the given date range', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $prospectCountStart = rand(1, 5);
    $prospectCountEnd = rand(1, 5);
    $alertCount = rand(1, 5);
    $segmentCount = rand(1, 5);
    $taskCount = rand(1, 5);

    Prospect::factory()->count($prospectCountStart)->state([
        'created_at' => $startDate,
    ])->create();

    Prospect::factory()->count($prospectCountEnd)->state([
        'created_at' => $endDate,
    ])->create();

    Alert::factory()->count($alertCount)->state([
        'concern_id' => Prospect::factory(),
        'concern_type' => (new Prospect())->getMorphClass(),
        'created_at' => $startDate,
    ])->create();

    Segment::factory()->count($segmentCount)->state([
        'model' => SegmentModel::Prospect,
        'created_at' => $endDate,
    ])->create();

    Task::factory()->count($taskCount)->state([
        'concern_id' => Prospect::factory(),
        'concern_type' => (new Prospect())->getMorphClass(),
        'created_at' => $startDate,
    ])->create();

    $widget = new ProspectReportStats();
    $widget->cacheTag = 'prospect-report-cache';
    $widget->filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $stats = $widget->getStats();

    expect($stats[0]->getValue())->toEqual($prospectCountStart + $prospectCountEnd)
        ->and($stats[1]->getValue())->toEqual($alertCount)
        ->and($stats[2]->getValue())->toEqual($segmentCount)
        ->and($stats[3]->getValue())->toEqual($taskCount);
});

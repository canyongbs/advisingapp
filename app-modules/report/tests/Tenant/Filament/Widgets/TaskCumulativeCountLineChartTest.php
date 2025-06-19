<?php

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Report\Filament\Widgets\TaskCumulativeCountLineChart;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Task\Enums\TaskStatus;
use AdvisingApp\Task\Models\Task;

it('returns correct cumulative task counts grouped by month within the given date range', function () {
    $startDate = now()->subDays(90);
    $endDate = now()->subDays(5);

    $student = Student::factory()->create();
    $prospect = Prospect::factory()->create();

    Task::factory()->count(2)->state([
        'concern_id' => $student->sisid,
        'concern_type' => (new Student())->getMorphClass(),
        'status' => TaskStatus::Pending,
        'created_at' => $endDate,
    ])->create();

    Task::factory()->count(2)->state([
        'concern_id' => $prospect->id,
        'concern_type' => (new Prospect())->getMorphClass(),
        'status' => TaskStatus::Pending,
        'created_at' => $endDate,
    ])->create();

    Task::factory()->count(2)->state([
        'concern_id' => null,
        'concern_type' => null,
        'status' => TaskStatus::Pending,
        'created_at' => $endDate,
    ])->create();

    $widgetInstance = new TaskCumulativeCountLineChart();
    $widgetInstance->cacheTag = 'report-tasks';
    $widgetInstance->filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    expect($widgetInstance->getData())->toMatchSnapshot();
});

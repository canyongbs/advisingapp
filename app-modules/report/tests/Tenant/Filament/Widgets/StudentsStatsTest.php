<?php

use AdvisingApp\Alert\Models\Alert;
use AdvisingApp\Report\Filament\Widgets\StudentsStats;
use AdvisingApp\Segment\Enums\SegmentModel;
use AdvisingApp\Segment\Models\Segment;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Task\Models\Task;

it('returns correct total student stats of students, alerts, segments and tasks within the given date range', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $count = random_int(1, 5);

    Student::factory()->count($count)->state([
        'created_at_source' => $startDate,
    ])->create();

    Student::factory()->count($count)->state([
        'created_at_source' => $endDate,
    ])->create();

    Alert::factory()->count($count)->state([
        'concern_id' => Student::factory()
            ->state([
                'created_at_source' => $endDate,
            ]),
        'concern_type' => (new Student())->getMorphClass(),
        'created_at' => $startDate,
    ])->create();

    Segment::factory()->count($count)->state([
        'model' => SegmentModel::Student,
        'created_at' => $endDate,
    ])->create();

    Task::factory()->count($count)->state([
        'concern_id' => Student::factory()
            ->state([
                'created_at_source' => $endDate,
            ]),
        'concern_type' => (new Student())->getMorphClass(),
        'created_at' => $startDate,
    ])->create();

    $widget = new StudentsStats();
    $widget->cacheTag = 'report-student';
    $widget->filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $stats = $widget->getStats();

    expect($stats[0]->getValue())->toEqual($count * 4)
        ->and($stats[1]->getValue())->toEqual($count)
        ->and($stats[2]->getValue())->toEqual($count)
        ->and($stats[3]->getValue())->toEqual($count);
});

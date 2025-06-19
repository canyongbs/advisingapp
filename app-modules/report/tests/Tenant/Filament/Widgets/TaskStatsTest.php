<?php

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Report\Filament\Widgets\TaskStats;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Task\Enums\TaskStatus;
use AdvisingApp\Task\Models\Task;

it('returns correct task statistics for total tasks, staff, students, and prospects with open tasks within the given date range', function () {
    $startDate = now()->subDays(10)->startOfDay();
    $endDate = now()->subDays(5)->endOfDay();

    $taskCount = rand(1, 5);
    $student = Student::factory()->create();
    $prospect = Prospect::factory()->create();

    Task::factory()->count($taskCount)->state([
        'status' => TaskStatus::Completed,
        'created_at' => $startDate,
    ])->create();

    Task::factory()->count($taskCount)->state([
        'status' => TaskStatus::Canceled,
        'created_at' => $endDate,
    ])->create();

    $staffTasks = Task::factory()->assigned()->count($taskCount)->state([
        'concern_id' => $student->sisid,
        'concern_type' => (new Student())->getMorphClass(),
        'status' => TaskStatus::InProgress,
        'created_at' => $startDate,
    ])->create();

    Task::factory()->count($taskCount)->state([
        'concern_id' => $student->sisid,
        'concern_type' => (new Student())->getMorphClass(),
        'status' => TaskStatus::Pending,
        'created_at' => $endDate,
    ])->create();

    Task::factory()->count($taskCount)->state([
        'concern_id' => $prospect->id,
        'concern_type' => (new Prospect())->getMorphClass(),
        'status' => TaskStatus::Pending,
        'created_at' => $endDate,
    ])->create();

    $widget = new TaskStats();
    $widget->cacheTag = 'report-tasks';
    $widget->filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $stats = $widget->getStats();

    expect($stats[0]->getValue())->toEqual($taskCount * 5)
        ->and($stats[1]->getValue())->toEqual($staffTasks->count())
        ->and($stats[2]->getValue())->toEqual(1)
        ->and($stats[3]->getValue())->toEqual(1);
});

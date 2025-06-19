<?php

use AdvisingApp\Report\Filament\Widgets\MostRecentTasksTable;
use AdvisingApp\Task\Models\Task;

use function Pest\Livewire\livewire;

it('displays only tasks added within the selected date range', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $taskWithinRange1 = Task::factory()->state([
        'created_at' => $startDate,
    ])->create();

    $taskWithinRange2 = Task::factory()->state([
        'created_at' => $endDate,
    ])->create();

    $taskOutsideRange = Task::factory()->state([
        'created_at' => now()->subDays(20),
    ])->create();

    $filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    livewire(MostRecentTasksTable::class, [
        'cacheTag' => 'report-tasks',
        'filters' => $filters,
    ])
        ->assertCanSeeTableRecords(collect([
            $taskWithinRange1,
            $taskWithinRange2,
        ]))
        ->assertCanNotSeeTableRecords(collect([$taskOutsideRange]));
});

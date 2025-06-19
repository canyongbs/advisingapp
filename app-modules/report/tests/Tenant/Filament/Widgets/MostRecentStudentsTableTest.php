<?php

use AdvisingApp\Report\Filament\Widgets\MostRecentStudentsTable;
use AdvisingApp\StudentDataModel\Models\Student;

use function Pest\Livewire\livewire;

it('displays only students added within the selected date range', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $studentWithinRange1 = Student::factory()->state([
        'created_at_source' => $startDate,
    ])->create();

    $studentWithinRange2 = Student::factory()->state([
        'created_at_source' => $endDate,
    ])->create();

    $studentOutsideRange = Student::factory()->state([
        'created_at_source' => now()->subDays(20),
    ])->create();

    $filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    livewire(MostRecentStudentsTable::class, [
        'cacheTag' => 'report-students',
        'filters' => $filters,
    ])
        ->assertCanSeeTableRecords(collect([
            $studentWithinRange1,
            $studentWithinRange2,
        ]))
        ->assertCanNotSeeTableRecords(collect([$studentOutsideRange]));
});

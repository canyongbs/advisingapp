<?php

use AdvisingApp\Report\Filament\Widgets\StudentEmailOptInOptOutPieChart;
use AdvisingApp\StudentDataModel\Models\Student;

it('it filters student email opt-in/out/null data accurately using start and end dates', function () {
    $count = rand(1, 10);

    $startDate = now()->subDays(90);
    $endDate = now()->subDays(5);

    $emailOptIn = Student::factory()
        ->count($count)
        ->state([
            'email_bounce' => false,
            'created_at_source' => $startDate,
        ])->create();

    $emailOptOut = Student::factory()
        ->count($count)
        ->state([
            'email_bounce' => true,
            'created_at_source' => $endDate,
        ])->create();

    $emailNull = Student::factory()
        ->count($count)
        ->state([
            'email_bounce' => null,
            'created_at_source' => now()->subDays(180),
        ])->create();

    $widgetInstance = new StudentEmailOptInOptOutPieChart();
    $widgetInstance->cacheTag = 'report-student-deliverability';
    $widgetInstance->filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $stats = $widgetInstance->getData()['datasets'][0]['data'];

    expect($stats[0])->toEqual($emailOptIn->count())
        ->and($stats[1])->toEqual($emailOptOut->count())
        ->and($stats[2])->not->toEqual($emailNull->count());
});

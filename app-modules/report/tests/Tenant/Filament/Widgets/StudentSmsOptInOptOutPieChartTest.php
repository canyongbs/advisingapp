<?php

use AdvisingApp\Report\Filament\Widgets\StudentSmsOptInOptOutPieChart;
use AdvisingApp\StudentDataModel\Models\Student;

it('it filters student SMS opt-in/out/null data accurately using start and end dates', function () {
    $count = rand(1, 10);

    $startDate = now()->subDays(90);
    $endDate = now()->subDays(5);

    $smsOptIn = Student::factory()
        ->count($count)
        ->state([
            'sms_opt_out' => false,
            'created_at_source' => $startDate,
        ])->create();

    $smsOptOut = Student::factory()
        ->count($count)
        ->state([
            'sms_opt_out' => true,
            'created_at_source' => $endDate,
        ])->create();

    $smsNull = Student::factory()
        ->count($count)
        ->state([
            'sms_opt_out' => null,
            'created_at_source' => now()->subDays(180),
        ])->create();

    $widgetInstance = new StudentSmsOptInOptOutPieChart();
    $widgetInstance->cacheTag = 'report-student-deliverability';
    $widgetInstance->filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $stats = $widgetInstance->getData()['datasets'][0]['data'];

    expect($stats[0])->toEqual($smsOptIn->count())
        ->and($stats[1])->toEqual($smsOptOut->count())
        ->and($stats[2])->not->toEqual($smsNull->count());
});

<?php

use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\Report\Filament\Widgets\StudentInteractionLineChart;
use AdvisingApp\StudentDataModel\Models\Student;

it('checks student interactions monthly line chart', function () {
    $studentCount = 5;

    Student::factory()->count($studentCount)->has(Interaction::factory()->count(5)->state([
        'created_at' => now()->subMonths(1),
    ]), 'interactions')->create();
    Student::factory()->count($studentCount)->has(Interaction::factory()->count(5)->state([
        'created_at' => now()->subMonths(6),
    ]), 'interactions')->create();

    $widgetInstance = new StudentInteractionLineChart();
    $widgetInstance->cacheTag = 'report-student-interaction';

    expect($widgetInstance->getData())->toMatchSnapshot();
});

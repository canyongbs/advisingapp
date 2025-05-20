<?php

use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\Interaction\Models\InteractionStatus;
use AdvisingApp\Report\Filament\Widgets\StudentInteractionStatusPolarAreaChart;
use AdvisingApp\StudentDataModel\Models\Student;

it('checks student interaction status polar area chart', function () {
    $interactionsCount = rand(1, 10);

    $interactionStatusFirst = InteractionStatus::factory()->create();
    $interactionStatusSecond = InteractionStatus::factory()->create();
    $interactionStatusThird = InteractionStatus::factory()->create();

    Student::factory()->has(Interaction::factory()->count($interactionsCount)->for($interactionStatusFirst, 'status'), 'interactions')->create();
    Student::factory()->has(Interaction::factory()->count($interactionsCount)->for($interactionStatusSecond, 'status'), 'interactions')->create();
    Student::factory()->has(Interaction::factory()->count($interactionsCount)->for($interactionStatusThird, 'status'), 'interactions')->create();

    $widgetInstance = new StudentInteractionStatusPolarAreaChart();
    $widgetInstance->cacheTag = 'report-student-interaction';

    $stats = $widgetInstance->getData()['datasets'][0]['data'];

    expect($interactionsCount)->toEqual($stats[0])
        ->and($interactionsCount)->toEqual($stats[1])
        ->and($interactionsCount)->toEqual($stats[2]);
});

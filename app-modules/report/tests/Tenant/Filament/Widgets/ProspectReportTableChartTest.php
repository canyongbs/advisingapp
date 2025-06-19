<?php

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Report\Filament\Widgets\ProspectReportTableChart;

use function Pest\Livewire\livewire;

it('displays only prospects added within the selected date range', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $prospectWithinRange1 = Prospect::factory()->state([
        'created_at' => $startDate,
    ])->create();

    $prospectWithinRange2 = Prospect::factory()->state([
        'created_at' => $endDate,
    ])->create();

    $prospectOutsideRange = Prospect::factory()->state([
        'created_at' => now()->subDays(20),
    ])->create();

    $filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    livewire(ProspectReportTableChart::class, [
        'cacheTag' => 'prospect-report-cache',
        'filters' => $filters,
    ])
        ->assertCanSeeTableRecords(collect([
            $prospectWithinRange1,
            $prospectWithinRange2,
        ]))
        ->assertCanNotSeeTableRecords(collect([$prospectOutsideRange]));
});

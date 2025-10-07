<?php

use AdvisingApp\Report\Filament\Widgets\ResearchAdvisorReportTable;
use AdvisingApp\Research\Models\ResearchRequest;

use function Pest\Livewire\livewire;

it('displays only research advisors created within the selected date range', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $researchAdvisorsWithinRange = ResearchRequest::factory()
        ->count(3)
        ->create(['created_at' => now()->subDays(7)]);
    
    $researchAdvisorsOutsideRange = ResearchRequest::factory()
        ->count(3)
        ->create(['created_at' => now()->subDays(20)]);

    livewire(ResearchAdvisorReportTable::class, [
        'cacheTag' => 'report-research-advisors',
    ])
        ->assertCanSeeTableRecords($researchAdvisorsWithinRange->merge($researchAdvisorsOutsideRange));

    livewire(ResearchAdvisorReportTable::class, [
        'cacheTag' => 'report-research-advisors',
        'pageFilters' => [
            'startDate' => $startDate->toDateString(),
            'endDate' => $endDate->toDateString(),
        ],
    ])
        ->assertCanSeeTableRecords($researchAdvisorsWithinRange)
        ->assertCanNotSeeTableRecords($researchAdvisorsOutsideRange);
});
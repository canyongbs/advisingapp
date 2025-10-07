<?php

use AdvisingApp\Report\Filament\Widgets\ResearchAdvisorReportStats;
use AdvisingApp\Research\Models\ResearchRequest;
use AdvisingApp\Research\Models\ResearchRequestParsedFile;
use AdvisingApp\Research\Models\ResearchRequestParsedLink;
use AdvisingApp\Research\Models\ResearchRequestParsedSearchResults;
use App\Models\User;

it('returns correct total research advisor stats within the given date range', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $count = random_int(1, 5);

    ResearchRequest::factory()
        ->has(ResearchRequestParsedLink::factory()->count(2), 'parsedLinks')
        ->count($count)
        ->create([
            'created_at' => $startDate,
            'user_id' => User::factory(),
        ]);
    
    ResearchRequest::factory()
        ->has(ResearchRequestParsedLink::factory()->count(2), 'parsedLinks')
        ->count($count)
        ->create([
            'created_at' => $endDate,
            'user_id' => User::factory(),
        ]);
    
    ResearchRequest::factory()
        ->has(ResearchRequestParsedSearchResults::factory()->count(2), 'parsedSearchResults')
        ->count($count)
        ->create([
            'created_at' => now()->subDays(20),
            'user_id' => User::factory(),
        ]);

    $widget = new ResearchAdvisorReportStats();
    $widget->cacheTag = 'report-research-advisors';

    $stats = $widget->getStats();

    expect($stats)->toHaveCount(3)
        ->and($stats[0]->getLabel())->toBe('Research Advisors')
        ->and($stats[0]->getValue())->toEqual($count * 3)
        ->and($stats[1]->getLabel())->toBe('Unique Users')
        ->and($stats[1]->getValue())->toEqual(3) //Unique user for each 'set' made above
        ->and($stats[2]->getLabel())->toBe('Sources Used')
        ->and($stats[2]->getValue())->toEqual($count * 6); //Two sources for each research advisor

    $widget->pageFilters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $stats = $widget->getStats();

    expect($stats)->toHaveCount(3)
        ->and($stats[0]->getLabel())->toBe('Research Advisors')
        ->and($stats[0]->getValue())->toEqual($count * 2) //Only research advisors within range
        ->and($stats[1]->getLabel())->toBe('Unique Users')
        ->and($stats[1]->getValue())->toEqual(2) //Unique user for each 'set' made above
        ->and($stats[2]->getLabel())->toBe('Sources Used')
        ->and($stats[2]->getValue())->toEqual($count * 4); //Two sources for each research advisor
});
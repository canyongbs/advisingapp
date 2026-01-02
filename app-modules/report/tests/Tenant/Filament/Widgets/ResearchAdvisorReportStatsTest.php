<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\Report\Filament\Widgets\ResearchAdvisorReportStats;
use AdvisingApp\Research\Models\ResearchRequest;
use AdvisingApp\Research\Models\ResearchRequestParsedLink;
use AdvisingApp\Research\Models\ResearchRequestParsedSearchResults;
use App\Models\User;

it('returns correct total research advisor stats within the given date range', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $count = random_int(1, 5);

    $userOne = User::factory()->create();
    $userTwo = User::factory()->create();
    $userThree = User::factory()->create();

    ResearchRequest::factory()
        ->has(ResearchRequestParsedLink::factory()->count(2), 'parsedLinks')
        ->count($count)
        ->create([
            'created_at' => $startDate,
            'user_id' => $userOne,
        ]);

    ResearchRequest::factory()
        ->has(ResearchRequestParsedLink::factory()->count(2), 'parsedLinks')
        ->count($count)
        ->create([
            'created_at' => $endDate,
            'user_id' => $userTwo,
        ]);

    ResearchRequest::factory()
        ->has(ResearchRequestParsedSearchResults::factory()->count(2), 'parsedSearchResults')
        ->count($count)
        ->create([
            'created_at' => now()->subDays(20),
            'user_id' => $userThree,
        ]);

    $widget = new ResearchAdvisorReportStats();
    $widget->cacheTag = 'report-research-advisors';

    $stats = $widget->getStats();

    expect($stats)->toHaveCount(3)
        ->and($stats[0]->getLabel())->toBe('Research Advisors')
        ->and($stats[0]->getValue())->toEqual($count * 3)
        ->and($stats[1]->getLabel())->toBe('Active Users')
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
        ->and($stats[1]->getLabel())->toBe('Active Users')
        ->and($stats[1]->getValue())->toEqual(2) //Unique user for each 'set' made above
        ->and($stats[2]->getLabel())->toBe('Sources Used')
        ->and($stats[2]->getValue())->toEqual($count * 4); //Two sources for each research advisor
});

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

use AdvisingApp\CaseManagement\Models\CaseModel;
use AdvisingApp\Concern\Models\Concern;
use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Report\Filament\Widgets\ProspectReportStats;
use AdvisingApp\Task\Models\Task;

it('returns correct total prospect stats of prospects, concerns, cases and tasks within the given date range', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $prospectCountStart = random_int(1, 5);
    $prospectCountEnd = random_int(1, 5);
    $concernCount = random_int(1, 5);
    $casesCount = random_int(1, 5);
    $taskCount = random_int(1, 5);

    Prospect::factory()->count($prospectCountStart)->state([
        'created_at' => $startDate,
    ])->create();

    Prospect::factory()->count($prospectCountEnd)->state([
        'created_at' => $endDate,
    ])->create();

    Concern::factory()->count($concernCount)->state([
        'concern_id' => Prospect::factory(),
        'concern_type' => (new Prospect())->getMorphClass(),
        'created_at' => $startDate,
    ])->create();

    CaseModel::factory()->count($casesCount)->state([
        'respondent_id' => Prospect::factory(),
        'respondent_type' => app(Prospect::class)->getMorphClass(),
        'created_at' => $endDate,
    ])->create();

    Task::factory()->count($taskCount)->state([
        'concern_id' => Prospect::factory(),
        'concern_type' => (new Prospect())->getMorphClass(),
        'created_at' => $startDate,
        'is_confidential' => false,
    ])->create();

    $widget = new ProspectReportStats();
    $widget->cacheTag = 'prospect-report-cache';
    $widget->pageFilters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $stats = $widget->getStats();

    expect($stats[0]->getValue())->toEqual($prospectCountStart + $prospectCountEnd)
        ->and($stats[1]->getValue())->toEqual($concernCount)
        ->and($stats[2]->getValue())->toEqual($casesCount)
        ->and($stats[3]->getValue())->toEqual($taskCount);
});

it('returns correct total prospect stats of prospects, concerns, cases and tasks based on group filter', function () {
    $count = random_int(1, 5);

    $group = Group::factory()->create([
        'model' => GroupModel::Prospect,
        'filters' => [
            'queryBuilder' => [
                'rules' => [
                    'C0Cy' => [
                        'type' => 'last_name',
                        'data' => [
                            'operator' => 'contains',
                            'settings' => [
                                'text' => 'John',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ]);

    Prospect::factory()
        ->count($count)
        ->state([
            'last_name' => 'John',
        ])->create();

    Prospect::factory()
        ->count($count)
        ->state([
            'last_name' => 'Doe',
        ])->create();

    Concern::factory()
        ->count($count)
        ->for(
            Prospect::factory()->create(['last_name' => 'John']),
            'concern'
        )
        ->create();

    Concern::factory()
        ->count($count)
        ->for(
            Prospect::factory()->create(['last_name' => 'Doe']),
            'concern'
        )
        ->create();

    CaseModel::factory()
        ->count($count)
        ->for(
            Prospect::factory()->create(['last_name' => 'John']),
            'respondent'
        )
        ->create();

    CaseModel::factory()
        ->count($count)
        ->for(
            Prospect::factory()->create(['last_name' => 'Doe']),
            'respondent'
        )
        ->create();

    Task::factory()
        ->count($count)
        ->for(
            Prospect::factory()->create(['last_name' => 'John']),
            'concern'
        )
        ->create([
            'is_confidential' => false,
        ]);

    Task::factory()
        ->count($count)
        ->for(
            Prospect::factory()->create(['last_name' => 'Doe']),
            'concern'
        )
        ->create([
            'is_confidential' => false,
        ]);

    $widget = new ProspectReportStats();
    $widget->cacheTag = 'prospect-report-cache';
    $widget->pageFilters = [
        'populationGroup' => $group->getKey(),
    ];

    $stats = $widget->getStats();

    expect($stats[0]->getValue())->toEqual($count + 3)
        ->and($stats[1]->getValue())->toEqual($count)
        ->and($stats[2]->getValue())->toEqual($count)
        ->and($stats[3]->getValue())->toEqual($count);
});

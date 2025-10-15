<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\CaseManagement\Enums\SystemCaseClassification;
use AdvisingApp\CaseManagement\Models\CaseModel;
use AdvisingApp\CaseManagement\Models\CaseStatus;
use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Report\Filament\Widgets\ProspectCaseTable;

use function Pest\Livewire\livewire;

it('returns all cases information created for prospects in given time range', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);
    $otherDate = now()->subDays(15);

    $openCases = CaseModel::factory()->state([
        'status_id' => CaseStatus::factory()->create([
            'classification' => SystemCaseClassification::Open,
        ])->getKey(),
        'created_at' => $startDate,
        'respondent_id' => Prospect::factory(),
        'respondent_type' => app(Prospect::class)->getMorphClass(),
    ])->create();

    $closedCases = CaseModel::factory()->state([
        'status_id' => CaseStatus::factory()->create([
            'classification' => SystemCaseClassification::Closed,
        ])->getKey(),
        'created_at' => $startDate,
        'respondent_id' => Prospect::factory(),
        'respondent_type' => app(Prospect::class)->getMorphClass(),
    ])->create();

    $inProgressCases = CaseModel::factory()->state([
        'status_id' => CaseStatus::factory()->create([
            'classification' => SystemCaseClassification::InProgress,
        ])->getKey(),
        'created_at' => $endDate,
        'respondent_id' => Prospect::factory(),
        'respondent_type' => app(Prospect::class)->getMorphClass(),
    ])->create();

    $otherCases = CaseModel::factory()->state([
        'status_id' => CaseStatus::factory()->create([
            'classification' => SystemCaseClassification::Open,
        ])->getKey(),
        'created_at' => $otherDate,
        'respondent_id' => Prospect::factory(),
        'respondent_type' => app(Prospect::class)->getMorphClass(),
    ])->create();

    $filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    livewire(ProspectCaseTable::class, [
        'cacheTag' => 'report-prospect-case',
        'pageFilters' => $filters,
    ])
        ->assertCanSeeTableRecords(collect([
            $openCases,
            $closedCases,
            $inProgressCases,
        ]))
        ->assertCanNotSeeTableRecords(collect([$otherCases]));
});

it('returns all cases information created for prospects based on segment filters', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);
    $otherDate = now()->subDays(15);

    $segment = Group::factory()->create([
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

    $openCases = CaseModel::factory()->state([
        'status_id' => CaseStatus::factory()->create([
            'classification' => SystemCaseClassification::Open,
        ])->getKey(),
        'created_at' => $startDate,
        'respondent_id' => Prospect::factory()->create(['last_name' => 'John']),
        'respondent_type' => (new Prospect())->getMorphClass(),
    ])->create();

    $closedCases = CaseModel::factory()->state([
        'status_id' => CaseStatus::factory()->create([
            'classification' => SystemCaseClassification::Closed,
        ])->getKey(),
        'created_at' => $startDate,
        'respondent_id' => Prospect::factory()->create(['last_name' => 'John']),
        'respondent_type' => (new Prospect())->getMorphClass(),
    ])->create();

    $inProgressCases = CaseModel::factory()->state([
        'status_id' => CaseStatus::factory()->create([
            'classification' => SystemCaseClassification::InProgress,
        ])->getKey(),
        'created_at' => $endDate,
        'respondent_id' => Prospect::factory()->create(['last_name' => 'John']),
        'respondent_type' => (new Prospect())->getMorphClass(),
    ])->create();

    $otherCases = CaseModel::factory()->state([
        'status_id' => CaseStatus::factory()->create([
            'classification' => SystemCaseClassification::Open,
        ])->getKey(),
        'created_at' => $otherDate,
        'respondent_id' => Prospect::factory()->create(['last_name' => 'Doe']),
        'respondent_type' => (new Prospect())->getMorphClass(),
    ])->create();

    $filters = [
        'populationGroup' => $segment->getKey(),
    ];

    // with segment filter
    livewire(ProspectCaseTable::class, [
        'cacheTag' => 'report-prospect-case',
        'pageFilters' => $filters,
    ])
        ->assertCanSeeTableRecords(collect([
            $openCases,
            $closedCases,
            $inProgressCases,
        ]))
        ->assertCanNotSeeTableRecords(collect([$otherCases]));

    // without filter
    livewire(ProspectCaseTable::class, [
        'cacheTag' => 'report-prospect-case',
        'filters' => [],
    ])
        ->assertCanSeeTableRecords(collect([
            $openCases,
            $closedCases,
            $inProgressCases,
            $otherCases,
        ]));
});

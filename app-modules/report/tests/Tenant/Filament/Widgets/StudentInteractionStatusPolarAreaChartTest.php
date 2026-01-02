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

use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Models\Group;
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

it('returns correct interaction counts by status for students within the selected date range', function () {
    $interactionsCount = rand(1, 10);

    $interactionStartDate = now()->subDays(90);
    $interactionEndDate = now()->subDays(5);

    $interactionStatusFirst = InteractionStatus::factory()->create();
    $interactionStatusSecond = InteractionStatus::factory()->create();
    $interactionStatusThird = InteractionStatus::factory()->create();

    Student::factory()
        ->has(
            Interaction::factory()
                ->count($interactionsCount)
                ->state([
                    'created_at' => $interactionStartDate,
                ])
                ->for(
                    $interactionStatusFirst,
                    'status'
                ),
            'interactions'
        )->create();

    Student::factory()
        ->has(
            Interaction::factory()
                ->count($interactionsCount)
                ->state([
                    'created_at' => $interactionEndDate,
                ])
                ->for(
                    $interactionStatusSecond,
                    'status'
                ),
            'interactions'
        )->create();

    Student::factory()
        ->has(
            Interaction::factory()
                ->count($interactionsCount)
                ->state([
                    'created_at' => now()->subDays(180),
                ])
                ->for(
                    $interactionStatusThird,
                    'status'
                ),
            'interactions'
        )->create();

    $widgetInstance = new StudentInteractionStatusPolarAreaChart();
    $widgetInstance->cacheTag = 'report-student-interaction';
    $widgetInstance->pageFilters = [
        'startDate' => $interactionStartDate->toDateString(),
        'endDate' => $interactionEndDate->toDateString(),
    ];

    $stats = $widgetInstance->getData()['datasets'][0]['data'];

    expect($interactionsCount)->toEqual($stats[0])
        ->and($interactionsCount)->toEqual($stats[1])
        ->and($interactionsCount)->not->toEqual($stats[2]);
});

it('returns correct interaction counts by status for students based on group filter', function () {
    $interactionsCount = random_int(1, 10);
    $interactionsCountForDoe = random_int(1, 10);

    $interactionStatusFirst = InteractionStatus::factory()->create();
    $interactionStatusSecond = InteractionStatus::factory()->create();

    $group = Group::factory()->create([
        'model' => GroupModel::Student,
        'filters' => [
            'queryBuilder' => [
                'rules' => [
                    'C0Cy' => [
                        'type' => 'last',
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

    Student::factory()
        ->has(
            Interaction::factory()
                ->count($interactionsCount)
                ->for(
                    $interactionStatusFirst,
                    'status'
                ),
            'interactions'
        )->create([
            'last' => 'John',
        ]);

    Student::factory()
        ->has(
            Interaction::factory()
                ->count($interactionsCountForDoe)
                ->for(
                    $interactionStatusSecond,
                    'status'
                ),
            'interactions'
        )->create([
            'last' => 'Doe',
        ]);

    $widgetInstance = new StudentInteractionStatusPolarAreaChart();
    $widgetInstance->cacheTag = 'report-student-interaction';
    $widgetInstance->pageFilters = [
        'populationGroup' => $group->getKey(),
    ];

    $stats = $widgetInstance->getData()['datasets'][0]['data'];

    expect($interactionsCount)->toEqual($stats[0])
        ->and($interactionsCount)->not->toEqual($stats[1]);

    $widgetInstance = new StudentInteractionStatusPolarAreaChart();
    $widgetInstance->cacheTag = 'report-student-interaction';
    $widgetInstance->pageFilters = [];

    $stats = $widgetInstance->getData()['datasets'][0]['data'];

    expect($interactionsCount)->toEqual($stats[0])
        ->and($interactionsCountForDoe)->toEqual($stats[1])
        ->and($interactionsCount + $interactionsCountForDoe)->toEqual($stats->sum());
});

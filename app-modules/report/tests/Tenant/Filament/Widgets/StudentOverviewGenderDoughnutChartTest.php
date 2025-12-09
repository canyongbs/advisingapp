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

use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\Report\Filament\Widgets\StudentOverviewGenderDoughnutChart;
use AdvisingApp\StudentDataModel\Models\Student;

it('checks student gender doughnut chart', function () {
    $maleCount = random_int(1, 10);
    $femaleCount = random_int(1, 10);
    $nonBinaryCount = random_int(1, 10);

    Student::factory()->count($maleCount)->create(['gender' => 'Male']);
    Student::factory()->count($femaleCount)->create(['gender' => 'Female']);
    Student::factory()->count($nonBinaryCount)->create(['gender' => 'Non-Binary']);

    $widgetInstance = new StudentOverviewGenderDoughnutChart();
    $widgetInstance->cacheTag = 'report-students';

    $stats = $widgetInstance->getData()['datasets'][0]['data'];
    $labels = $widgetInstance->getData()['labels'];

    expect($stats->sum())->toEqual($maleCount + $femaleCount + $nonBinaryCount)
        ->and($labels->count())->toEqual(3);
});

it('returns correct student counts by gender within the selected date range', function () {
    $maleCount = random_int(1, 10);
    $femaleCount = random_int(1, 10);
    $nonBinaryCount = random_int(1, 10);

    $startDate = now()->subDays(90);
    $endDate = now()->subDays(5);

    Student::factory()
        ->count($maleCount)
        ->create([
            'gender' => 'Male',
            'created_at_source' => $startDate,
        ]);

    Student::factory()
        ->count($femaleCount)
        ->create([
            'gender' => 'Female',
            'created_at_source' => $endDate,
        ]);

    Student::factory()
        ->count($nonBinaryCount)
        ->create([
            'gender' => 'Non-Binary',
            'created_at_source' => now()->subDays(180),
        ]);

    $widgetInstance = new StudentOverviewGenderDoughnutChart();
    $widgetInstance->cacheTag = 'report-students';
    $widgetInstance->pageFilters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $stats = $widgetInstance->getData()['datasets'][0]['data'];
    $labels = $widgetInstance->getData()['labels'];

    expect($stats->sum())->toEqual($maleCount + $femaleCount)
        ->and($stats->sum())->not->toEqual($maleCount + $femaleCount + $nonBinaryCount)
        ->and($labels->count())->toEqual(2);
});

it('returns correct student counts by gender based on group filter', function () {
    $maleCountJohn = random_int(1, 10);
    $femaleCountJohn = random_int(1, 10);
    $maleCountDoe = random_int(1, 10);
    $femaleCountDoe = random_int(1, 10);

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
        ->count($maleCountJohn)
        ->create([
            'gender' => 'Male',
            'last' => 'John',
        ]);

    Student::factory()
        ->count($femaleCountJohn)
        ->create([
            'gender' => 'Female',
            'last' => 'John',
        ]);

    Student::factory()
        ->count($maleCountDoe)
        ->create([
            'gender' => 'Male',
            'last' => 'Doe',
        ]);

    Student::factory()
        ->count($femaleCountDoe)
        ->create([
            'gender' => 'Female',
            'last' => 'Doe',
        ]);

    $widgetInstance = new StudentOverviewGenderDoughnutChart();
    $widgetInstance->cacheTag = 'report-students';
    $widgetInstance->pageFilters = [
        'populationGroup' => $group->getKey(),
    ];

    $stats = $widgetInstance->getData()['datasets'][0]['data'];

    expect($stats->sum())->toEqual($maleCountJohn + $femaleCountJohn)
        ->and($stats->sum())->not->toEqual($maleCountJohn + $femaleCountJohn + $maleCountDoe + $femaleCountDoe);

    $widgetInstance = new StudentOverviewGenderDoughnutChart();
    $widgetInstance->cacheTag = 'report-students';
    $widgetInstance->pageFilters = [];

    $stats = $widgetInstance->getData()['datasets'][0]['data'];

    expect($stats->sum())->toEqual($maleCountJohn + $femaleCountJohn + $maleCountDoe + $femaleCountDoe);
});

it('handles case-sensitive gender grouping', function () {
    $maleUpperCount = random_int(1, 10);
    $maleLowerCount = random_int(1, 10);
    $maleMixedCount = random_int(1, 10);

    Student::factory()->count($maleUpperCount)->create(['gender' => 'MALE']);
    Student::factory()->count($maleLowerCount)->create(['gender' => 'male']);
    Student::factory()->count($maleMixedCount)->create(['gender' => 'Male']);

    $widgetInstance = new StudentOverviewGenderDoughnutChart();
    $widgetInstance->cacheTag = 'report-students';

    $stats = $widgetInstance->getData()['datasets'][0]['data'];
    $labels = $widgetInstance->getData()['labels'];

    expect($labels->count())->toEqual(1)
        ->and($stats->sum())->toEqual($maleUpperCount + $maleLowerCount + $maleMixedCount);
});

it('excludes students with null or empty gender', function () {
    $maleCount = random_int(1, 10);
    $nullGenderCount = random_int(1, 10);
    $emptyGenderCount = random_int(1, 10);

    Student::factory()->count($maleCount)->create(['gender' => 'Male']);
    Student::factory()->count($nullGenderCount)->create(['gender' => null]);
    Student::factory()->count($emptyGenderCount)->create(['gender' => '']);

    $widgetInstance = new StudentOverviewGenderDoughnutChart();
    $widgetInstance->cacheTag = 'report-students';

    $stats = $widgetInstance->getData()['datasets'][0]['data'];
    $labels = $widgetInstance->getData()['labels'];

    expect($stats->sum())->toEqual($maleCount)
        ->and($labels->count())->toEqual(1);
});

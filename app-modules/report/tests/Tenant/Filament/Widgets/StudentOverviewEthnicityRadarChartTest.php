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
use AdvisingApp\Report\Filament\Widgets\StudentOverviewEthnicityRadarChart;
use AdvisingApp\StudentDataModel\Models\Student;

it('checks student ethnicity polar area chart', function () {
    $asianCount = random_int(1, 10);
    $hispanicCount = random_int(1, 10);
    $caucasianCount = random_int(1, 10);

    Student::factory()->count($asianCount)->create(['ethnicity' => 'Asian']);
    Student::factory()->count($hispanicCount)->create(['ethnicity' => 'Hispanic']);
    Student::factory()->count($caucasianCount)->create(['ethnicity' => 'Caucasian']);

    $widgetInstance = new StudentOverviewEthnicityRadarChart();
    $widgetInstance->cacheTag = 'report-students';

    $stats = $widgetInstance->getData()['datasets'][0]['data'];
    $labels = $widgetInstance->getData()['labels'];

    expect($stats->sum())->toEqual($asianCount + $hispanicCount + $caucasianCount)
        ->and($labels->count())->toEqual(3);
});

it('returns correct student counts by ethnicity within the selected date range', function () {
    $asianCount = random_int(1, 10);
    $hispanicCount = random_int(1, 10);
    $caucasianCount = random_int(1, 10);

    $startDate = now()->subDays(90);
    $endDate = now()->subDays(5);

    Student::factory()
        ->count($asianCount)
        ->create([
            'ethnicity' => 'Asian',
            'created_at_source' => $startDate,
        ]);

    Student::factory()
        ->count($hispanicCount)
        ->create([
            'ethnicity' => 'Hispanic',
            'created_at_source' => $endDate,
        ]);

    Student::factory()
        ->count($caucasianCount)
        ->create([
            'ethnicity' => 'Caucasian',
            'created_at_source' => now()->subDays(180),
        ]);

    $widgetInstance = new StudentOverviewEthnicityRadarChart();
    $widgetInstance->cacheTag = 'report-students';
    $widgetInstance->pageFilters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $stats = $widgetInstance->getData()['datasets'][0]['data'];
    $labels = $widgetInstance->getData()['labels'];

    expect($stats->sum())->toEqual($asianCount + $hispanicCount)
        ->and($stats->sum())->not->toEqual($asianCount + $hispanicCount + $caucasianCount)
        ->and($labels->count())->toEqual(2);
});

it('returns correct student counts by ethnicity based on group filter', function () {
    $asianCountJohn = random_int(1, 10);
    $hispanicCountJohn = random_int(1, 10);
    $asianCountDoe = random_int(1, 10);
    $hispanicCountDoe = random_int(1, 10);

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
        ->count($asianCountJohn)
        ->create([
            'ethnicity' => 'Asian',
            'last' => 'John',
        ]);

    Student::factory()
        ->count($hispanicCountJohn)
        ->create([
            'ethnicity' => 'Hispanic',
            'last' => 'John',
        ]);

    Student::factory()
        ->count($asianCountDoe)
        ->create([
            'ethnicity' => 'Asian',
            'last' => 'Doe',
        ]);

    Student::factory()
        ->count($hispanicCountDoe)
        ->create([
            'ethnicity' => 'Hispanic',
            'last' => 'Doe',
        ]);

    $widgetInstance = new StudentOverviewEthnicityRadarChart();
    $widgetInstance->cacheTag = 'report-students';
    $widgetInstance->pageFilters = [
        'populationGroup' => $group->getKey(),
    ];

    $stats = $widgetInstance->getData()['datasets'][0]['data'];

    expect($stats->sum())->toEqual($asianCountJohn + $hispanicCountJohn)
        ->and($stats->sum())->not->toEqual($asianCountJohn + $hispanicCountJohn + $asianCountDoe + $hispanicCountDoe);

    $widgetInstance = new StudentOverviewEthnicityRadarChart();
    $widgetInstance->cacheTag = 'report-students';
    $widgetInstance->pageFilters = [];

    $stats = $widgetInstance->getData()['datasets'][0]['data'];

    expect($stats->sum())->toEqual($asianCountJohn + $hispanicCountJohn + $asianCountDoe + $hispanicCountDoe);
});

it('handles case-sensitive ethnicity grouping', function () {
    $asianUpperCount = random_int(1, 10);
    $asianLowerCount = random_int(1, 10);
    $asianMixedCount = random_int(1, 10);

    Student::factory()->count($asianUpperCount)->create(['ethnicity' => 'ASIAN']);
    Student::factory()->count($asianLowerCount)->create(['ethnicity' => 'asian']);
    Student::factory()->count($asianMixedCount)->create(['ethnicity' => 'Asian']);

    $widgetInstance = new StudentOverviewEthnicityRadarChart();
    $widgetInstance->cacheTag = 'report-students';

    $stats = $widgetInstance->getData()['datasets'][0]['data'];
    $labels = $widgetInstance->getData()['labels'];

    expect($labels->count())->toEqual(1)
        ->and($stats->sum())->toEqual($asianUpperCount + $asianLowerCount + $asianMixedCount);
});

it('excludes students with null or empty ethnicity', function () {
    $asianCount = random_int(1, 10);
    $nullEthnicityCount = random_int(1, 10);
    $emptyEthnicityCount = random_int(1, 10);

    Student::factory()->count($asianCount)->create(['ethnicity' => 'Asian']);
    Student::factory()->count($nullEthnicityCount)->create(['ethnicity' => null]);
    Student::factory()->count($emptyEthnicityCount)->create(['ethnicity' => '']);

    $widgetInstance = new StudentOverviewEthnicityRadarChart();
    $widgetInstance->cacheTag = 'report-students';

    $stats = $widgetInstance->getData()['datasets'][0]['data'];
    $labels = $widgetInstance->getData()['labels'];

    expect($stats->sum())->toEqual($asianCount)
        ->and($labels->count())->toEqual(1);
});

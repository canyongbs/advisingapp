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

use AdvisingApp\Report\Filament\Widgets\StudentEmailOptInOptOutPieChart;
use AdvisingApp\Segment\Enums\SegmentModel;
use AdvisingApp\Segment\Models\Segment;
use AdvisingApp\StudentDataModel\Models\Student;

it('it filters student email opt-in/out/null data accurately using start and end dates', function () {
    $count = random_int(1, 10);

    $startDate = now()->subDays(90);
    $endDate = now()->subDays(5);

    $emailOptIn = Student::factory()
        ->count($count)
        ->state([
            'email_bounce' => false,
            'created_at_source' => $startDate,
        ])->create();

    $emailOptOut = Student::factory()
        ->count($count)
        ->state([
            'email_bounce' => true,
            'created_at_source' => $endDate,
        ])->create();

    $emailNull = Student::factory()
        ->count($count)
        ->state([
            'email_bounce' => null,
            'created_at_source' => now()->subDays(180),
        ])->create();

    $widgetInstance = new StudentEmailOptInOptOutPieChart();
    $widgetInstance->cacheTag = 'report-student-deliverability';
    $widgetInstance->filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $stats = $widgetInstance->getData()['datasets'][0]['data'];

    expect($stats[0])->toEqual($emailOptIn->count())
        ->and($stats[1])->toEqual($emailOptOut->count())
        ->and($stats[2])->not->toEqual($emailNull->count());
});

it('it filters student email opt-in/out/null data accurately based on segment filters', function () {
    $count = random_int(1, 10);

    $startDate = now()->subDays(90);
    $endDate = now()->subDays(5);

    $segment = Segment::factory()->create([
        'model' => SegmentModel::Student,
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

    $emailOptInWithJoeName = Student::factory()
        ->count($count)
        ->state([
            'email_bounce' => false,
            'created_at_source' => $startDate,
            'last' => 'John',
        ])->create();

    $emailOptOutWithJoeName = Student::factory()
        ->count($count)
        ->state([
            'email_bounce' => true,
            'created_at_source' => $endDate,
            'last' => 'John',
        ])->create();

    $emailNullWithJoeName = Student::factory()
        ->count($count)
        ->state([
            'email_bounce' => null,
            'created_at_source' => now()->subDays(180),
            'last' => 'John',
        ])->create();

    $emailOptInWithDoeName = Student::factory()
        ->count($count)
        ->state([
            'email_bounce' => false,
            'created_at_source' => $startDate,
            'last' => 'Doe',
        ])->create();

    $emailOptOutWithDoeName = Student::factory()
        ->count($count)
        ->state([
            'email_bounce' => true,
            'created_at_source' => $endDate,
            'last' => 'Doe',
        ])->create();

    $emailNullWithDoeName = Student::factory()
        ->count($count)
        ->state([
            'email_bounce' => null,
            'created_at_source' => now()->subDays(180),
            'last' => 'Doe',
        ])->create();

    // with segment filter
    $widgetInstance = new StudentEmailOptInOptOutPieChart();
    $widgetInstance->cacheTag = 'report-student-deliverability';
    $widgetInstance->filters = [
        'populationSegment' => $segment->getKey(),
    ];

    $stats = $widgetInstance->getData()['datasets'][0]['data'];

    expect($stats[0])->toEqual($emailOptInWithJoeName->count())
        ->and($stats[1])->toEqual($emailOptOutWithJoeName->count())
        ->and($stats[2])->toEqual($emailNullWithJoeName->count());

    // without segment filter
    $widgetInstance = new StudentEmailOptInOptOutPieChart();
    $widgetInstance->cacheTag = 'report-student-deliverability';
    $widgetInstance->filters = [];

    $stats = $widgetInstance->getData()['datasets'][0]['data'];

    expect($stats[0])->toEqual($emailOptInWithJoeName->merge($emailOptInWithDoeName)->count())
        ->and($stats[1])->toEqual($emailOptOutWithJoeName->merge($emailOptOutWithDoeName)->count())
        ->and($stats[2])->toEqual($emailNullWithJoeName->merge($emailNullWithDoeName)->count());
});

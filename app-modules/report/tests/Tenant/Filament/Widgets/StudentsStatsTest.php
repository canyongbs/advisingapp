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

use AdvisingApp\Alert\Models\Alert;
use AdvisingApp\CaseManagement\Models\CaseModel;
use AdvisingApp\Report\Filament\Widgets\StudentsStats;
use AdvisingApp\Segment\Enums\SegmentModel;
use AdvisingApp\Segment\Models\Segment;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Task\Models\Task;

it('returns correct total student stats of students, alerts, segments and tasks within the given date range', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $count = random_int(1, 5);

    Student::factory()->count($count)->state([
        'created_at_source' => $startDate,
    ])->create();

    Student::factory()->count($count)->state([
        'created_at_source' => $endDate,
    ])->create();

    Alert::factory()->count($count)->state([
        'concern_id' => Student::factory()
            ->state([
                'created_at_source' => $endDate,
            ]),
        'concern_type' => (new Student())->getMorphClass(),
        'created_at' => $startDate,
    ])->create();

    CaseModel::factory()->count($count)->state([
        'respondent_id' => Student::factory()
            ->state([
                'created_at_source' => $endDate,
            ]),
        'respondent_type' => (new Student())->getMorphClass(),
        'created_at' => $endDate,
    ])->create();

    Task::factory()->count($count)->state([
        'concern_id' => Student::factory()
            ->state([
                'created_at_source' => $endDate,
            ]),
        'concern_type' => (new Student())->getMorphClass(),
        'created_at' => $startDate,
        'is_confidential' => false,
    ])->create();

    $widget = new StudentsStats();
    $widget->cacheTag = 'report-student';
    $widget->filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $stats = $widget->getStats();

    expect($stats[0]->getValue())->toEqual($count * 5)
        ->and($stats[1]->getValue())->toEqual($count)
        ->and($stats[2]->getValue())->toEqual($count)
        ->and($stats[3]->getValue())->toEqual($count);
});

it('returns correct total student stats of students, alerts, cases and tasks based on segment filters', function () {
    $count = random_int(1, 5);

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

    Student::factory()
        ->count($count)
        ->create(['last' => 'John']);

    Student::factory()
        ->count($count)
        ->create(['last' => 'Doe']);

    Alert::factory()
        ->count($count)
        ->for(
            Student::factory()->create(['last' => 'John']),
            'concern'
        )
        ->create();

    Alert::factory()
        ->count($count)
        ->for(
            Student::factory()->create(['last' => 'Smith']),
            'concern'
        )
        ->create();

    CaseModel::factory()
        ->count($count)
        ->for(
            Student::factory()->create(['last' => 'John']),
            'respondent'
        )
        ->create();

    CaseModel::factory()
        ->count($count)
        ->for(
            Student::factory()->create(['last' => 'Smith']),
            'respondent'
        )
        ->create();

    Task::factory()
        ->count($count)
        ->for(
            Student::factory()->create(['last' => 'John']),
            'concern'
        )
        ->create(['is_confidential' => false]);

    Task::factory()
        ->count($count)
        ->for(
            Student::factory()->create(['last' => 'Doe']),
            'concern'
        )
        ->create(['is_confidential' => false]);

    $widget = new StudentsStats();
    $widget->cacheTag = 'report-student';
    $widget->filters = [
        'populationSegment' => $segment->getKey(),
    ];

    $stats = $widget->getStats();

    expect($stats[0]->getValue())->toEqual($count + 3)
        ->and($stats[1]->getValue())->toEqual($count)
        ->and($stats[2]->getValue())->toEqual($count)
        ->and($stats[3]->getValue())->toEqual($count);
});

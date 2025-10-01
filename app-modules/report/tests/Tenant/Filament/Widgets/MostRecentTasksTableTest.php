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

use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Report\Filament\Widgets\MostRecentTasksTable;
use AdvisingApp\Segment\Enums\SegmentModel;
use AdvisingApp\Segment\Models\Segment;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Task\Models\Task;

use function Pest\Livewire\livewire;

it('displays only tasks added within the selected date range for students', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $taskWithinRange1 = Task::factory()->concerningStudent()->state([
        'created_at' => $startDate,
        'is_confidential' => false,
    ])->create();

    $taskWithinRange2 = Task::factory()->concerningStudent()->state([
        'created_at' => $endDate,
        'is_confidential' => false,
    ])->create();

    $taskOutsideRange = Task::factory()->concerningStudent()->state([
        'created_at' => now()->subDays(20),
        'is_confidential' => false,
    ])->create();

    $filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    livewire(MostRecentTasksTable::class, [
        'cacheTag' => 'report-tasks',
        'educatableType' => Student::class,
        'filters' => $filters,
    ])
        ->assertCanSeeTableRecords(collect([
            $taskWithinRange1,
            $taskWithinRange2,
        ]))
        ->assertCanNotSeeTableRecords(collect([$taskOutsideRange]));
});

it('displays only tasks added within the selected date range for prospects', function () {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $taskWithinRange1 = Task::factory()->concerningProspect()->state([
        'created_at' => $startDate,
        'is_confidential' => false,
    ])->create();

    $taskWithinRange2 = Task::factory()->concerningProspect()->state([
        'created_at' => $endDate,
        'is_confidential' => false,
    ])->create();

    $taskOutsideRange = Task::factory()->concerningProspect()->state([
        'created_at' => now()->subDays(20),
        'is_confidential' => false,
    ])->create();

    $filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    livewire(MostRecentTasksTable::class, [
        'cacheTag' => 'report-tasks',
        'educatableType' => Prospect::class,
        'filters' => $filters,
    ])
        ->assertCanSeeTableRecords(collect([
            $taskWithinRange1,
            $taskWithinRange2,
        ]))
        ->assertCanNotSeeTableRecords(collect([$taskOutsideRange]));
});

it('properly filters students by segment', function () {
    $startDate = now()->subDays(10);
    $otherDate = now()->subDays(15);

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

    $segmentTasks = Task::factory()->concerningStudent(Student::factory()->create(['last' => 'John']))->state([
        'created_at' => $startDate,
        'is_confidential' => false,
    ])->create();

    $nonSegmentTasks = Task::factory()->concerningStudent(Student::factory()->create(['last' => 'Doe']))->state([
        'created_at' => $otherDate,
        'is_confidential' => false,
    ])->create();

    $filters = [
        'populationSegment' => $segment->getKey(),
    ];

    livewire(MostRecentTasksTable::class, [
        'cacheTag' => 'report-tasks',
        'educatableType' => Student::class,
        'filters' => $filters,
    ])
        ->assertCanSeeTableRecords(collect([$segmentTasks]))
        ->assertCanNotSeeTableRecords(collect([$nonSegmentTasks]));

    livewire(MostRecentTasksTable::class, [
        'cacheTag' => 'report-tasks',
        'educatableType' => Student::class,
        'filters' => [],
    ])
        ->assertCanSeeTableRecords(collect([$segmentTasks, $nonSegmentTasks]));
});

it('properly filters prospects by segment', function () {
    $startDate = now()->subDays(10);
    $otherDate = now()->subDays(15);

    $segment = Segment::factory()->create([
        'model' => SegmentModel::Prospect,
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

    $segmentTasks = Task::factory()->concerningProspect(Prospect::factory()->create(['last_name' => 'John']))->state([
        'created_at' => $startDate,
        'is_confidential' => false,
    ])->create();

    $nonSegmentTasks = Task::factory()->concerningProspect(Prospect::factory()->create(['last_name' => 'Doe']))->state([
        'created_at' => $otherDate,
        'is_confidential' => false,
    ])->create();

    $filters = [
        'populationSegment' => $segment->getKey(),
    ];

    livewire(MostRecentTasksTable::class, [
        'cacheTag' => 'report-tasks',
        'educatableType' => Prospect::class,
        'filters' => $filters,
    ])
        ->assertCanSeeTableRecords(collect([$segmentTasks]))
        ->assertCanNotSeeTableRecords(collect([$nonSegmentTasks]));

    livewire(MostRecentTasksTable::class, [
        'cacheTag' => 'report-tasks',
        'educatableType' => Prospect::class,
        'filters' => [],
    ])
        ->assertCanSeeTableRecords(collect([$segmentTasks, $nonSegmentTasks]));
});

<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use AdvisingApp\Concern\Models\Concern;
use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\Report\Filament\Widgets\StudentsStats;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Task\Models\Task;
use Carbon\Carbon;

it('returns correct total student stats of students, concerns, groups and tasks within the given date range', function (): void {
    $startDate = now()->subDays(10);
    $endDate = now()->subDays(5);

    $count = random_int(1, 5);

    Student::factory()->count($count)->state([
        'created_at_source' => $startDate,
    ])->create();

    Student::factory()->count($count)->state([
        'created_at_source' => $endDate,
    ])->create();

    Concern::factory()->count($count)->state([
        'concern_id' => Student::factory()
            ->state([
                'created_at_source' => $endDate,
            ]),
        'concern_type' => (new Student())->getMorphClass(),
        'created_at' => $startDate,
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
    $widget->pageFilters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $stats = $widget->getStats();

    expect($stats[0]->getValue())->toEqual($count * 4)
        ->and($stats[1]->getValue())->toEqual($count)
        ->and($stats[2]->getValue())->toEqual($count);
});

it('returns correct total student stats of students, concerns, and tasks based on group filters', function (): void {
    $count = random_int(1, 5);

    $group = Group::factory()->create([
        'model' => GroupModel::Student,
        'filters' => [
            'queryBuilder' => [
                'rules' => [
                    'C0Cy' => [
                        'type' => 'name',
                        'data' => [
                            'operator' => 'lastName',
                            'settings' => [
                                'operator' => 'contains',
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

    Concern::factory()
        ->count($count)
        ->for(
            Student::factory()->create(['last' => 'John']),
            'concern'
        )
        ->create();

    Concern::factory()
        ->count($count)
        ->for(
            Student::factory()->create(['last' => 'Smith']),
            'concern'
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
    $widget->pageFilters = [
        'populationGroup' => $group->getKey(),
    ];

    $stats = $widget->getStats();

    expect($stats[0]->getValue())->toEqual($count + 2)
        ->and($stats[1]->getValue())->toEqual($count)
        ->and($stats[2]->getValue())->toEqual($count);
});

it('does not count archived students in the total', function (): void {
    $startDate = now()->subDays(10);
    $endDate = now();

    Student::factory()->count(3)->state(['created_at_source' => $startDate])->create();

    $archived = Student::factory()->count(2)->state(['created_at_source' => $startDate])->create();

    $widget = new StudentsStats();
    $widget->cacheTag = 'report-student';
    $widget->pageFilters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    expect($widget->getStats()[0]->getValue())->toEqual(5);

    $archived->each(fn (Student $student): bool => $student->archive());

    expect($widget->getStats()[0]->getValue())->toEqual(3);
});

// Archiving applies by current status, so a student archived in September no longer counts
// in a March report either.
it('does not count a student archived after the reported period', function (): void {
    Student::factory()->create(['created_at_source' => '2026-03-10']);

    $student = Student::factory()->create(['created_at_source' => '2026-03-10']);
    $student->forceFill(['archived_at' => Carbon::parse('2026-09-15')])->save();

    $widget = new StudentsStats();
    $widget->cacheTag = 'report-student';
    $widget->pageFilters = [
        'startDate' => '2026-03-01',
        'endDate' => '2026-03-31',
    ];

    expect($widget->getStats()[0]->getValue())->toEqual(1);
});

it('does not count concerns or tasks of archived students, including those from before they were archived', function (): void {
    $startDate = now()->subDays(10);
    $endDate = now();

    $active = Student::factory()->create(['created_at_source' => $startDate]);
    $archived = Student::factory()->create(['created_at_source' => $startDate]);

    Concern::factory()->state([
        'concern_id' => $active->getKey(),
        'concern_type' => (new Student())->getMorphClass(),
        'created_at' => now()->subDays(5),
    ])->create();

    Concern::factory()->state([
        'concern_id' => $archived->getKey(),
        'concern_type' => (new Student())->getMorphClass(),
        'created_at' => now()->subDays(5),
    ])->create();

    Task::factory()->state([
        'concern_id' => $active->getKey(),
        'concern_type' => (new Student())->getMorphClass(),
        'created_at' => now()->subDays(5),
        'is_confidential' => false,
    ])->create();

    Task::factory()->state([
        'concern_id' => $archived->getKey(),
        'concern_type' => (new Student())->getMorphClass(),
        'created_at' => now()->subDays(5),
        'is_confidential' => false,
    ])->create();

    $archived->archive();

    $widget = new StudentsStats();
    $widget->cacheTag = 'report-student';

    // Unfiltered, so read through the cache.
    $unfilteredStats = $widget->getStats();

    expect($unfilteredStats[1]->getValue())->toEqual(1)
        ->and($unfilteredStats[2]->getValue())->toEqual(1);

    $widget->pageFilters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $filteredStats = $widget->getStats();

    expect($filteredStats[1]->getValue())->toEqual(1)
        ->and($filteredStats[2]->getValue())->toEqual(1);
});

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
use AdvisingApp\Report\Filament\Widgets\TaskStats;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Task\Enums\TaskStatus;
use AdvisingApp\Task\Models\Task;

it('returns correct task statistics for total tasks, staff, students, and prospects with open tasks within the given date range', function () {
    $startDate = now()->subDays(10)->startOfDay();
    $endDate = now()->subDays(5)->endOfDay();

    $taskCount = random_int(1, 5);
    $student = Student::factory()->create();
    $prospect = Prospect::factory()->create();

    Task::factory()->count($taskCount)->state([
        'status' => TaskStatus::Completed,
        'created_at' => $startDate,
        'is_confidential' => false,
    ])->create();

    Task::factory()->count($taskCount)->state([
        'status' => TaskStatus::Canceled,
        'created_at' => $endDate,
        'is_confidential' => false,
    ])->create();

    $staffTasks = Task::factory()->assigned()->count($taskCount)->state([
        'concern_id' => $student->sisid,
        'concern_type' => (new Student())->getMorphClass(),
        'status' => TaskStatus::InProgress,
        'created_at' => $startDate,
        'is_confidential' => false,
    ])->create();

    Task::factory()->count($taskCount)->state([
        'concern_id' => $student->sisid,
        'concern_type' => (new Student())->getMorphClass(),
        'status' => TaskStatus::Pending,
        'created_at' => $endDate,
        'is_confidential' => false,
    ])->create();

    Task::factory()->count($taskCount)->state([
        'concern_id' => $prospect->getKey(),
        'concern_type' => (new Prospect())->getMorphClass(),
        'status' => TaskStatus::Pending,
        'created_at' => $endDate,
        'is_confidential' => false,
    ])->create();

    $widget = new TaskStats();
    $widget->cacheTag = 'report-tasks';
    $widget->filters = [
        'startDate' => $startDate->toDateString(),
        'endDate' => $endDate->toDateString(),
    ];

    $stats = $widget->getStats();

    expect($stats[0]->getValue())->toEqual($taskCount * 5)
        ->and($stats[1]->getValue())->toEqual($staffTasks->count())
        ->and($stats[2]->getValue())->toEqual(1)
        ->and($stats[3]->getValue())->toEqual(1);
});

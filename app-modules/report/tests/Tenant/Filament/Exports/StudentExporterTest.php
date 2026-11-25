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

use AdvisingApp\Report\Filament\Exports\StudentExporter;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Task\Enums\TaskStatus;
use AdvisingApp\Task\Models\Task;
use Filament\Actions\Exports\ExportColumn;
use Illuminate\Database\Eloquent\Builder;

it('does not count archived tasks', function (): void {
    $student = Student::factory()->create();

    Task::factory()->concerningStudent($student)->create(['status' => TaskStatus::Pending, 'is_confidential' => false]);
    Task::factory()->concerningStudent($student)->create(['status' => TaskStatus::Pending, 'is_confidential' => false, 'archived_at' => now()]);

    $query = Student::query()->whereKey($student->getKey());

    collect(StudentExporter::getColumns())
        ->filter(fn (ExportColumn $column): bool => str_starts_with($column->getName(), 'tasks_'))
        ->each(fn (ExportColumn $column): Builder => $column->applyRelationshipAggregates($query));

    $exportedStudent = $query->sole();

    expect($exportedStudent->getAttributes())->toMatchArray([
        'tasks_count' => 1,
        'tasks_' . TaskStatus::Pending->value . '_count' => 1,
    ]);
});

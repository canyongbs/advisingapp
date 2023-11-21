<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

use App\Models\User;
use Assist\Task\Models\Task;

use function Tests\asSuperAdmin;

use Assist\Task\Enums\TaskStatus;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use Assist\Task\Filament\Resources\TaskResource;
use Assist\Task\Filament\Resources\TaskResource\Pages\ListTasks;

test('ListTasks page displays the correct details for available my tasks', function () {
    asSuperAdmin();

    $tasks = Task::factory()
        ->count(10)
        ->assigned(User::first())
        ->concerningStudent()
        ->create(
            [
                'status' => TaskStatus::InProgress->value,
            ]
        );

    $component = livewire(ListTasks::class);

    $component->removeTableFilters()
        ->assertSuccessful()
        ->assertCanSeeTableRecords($tasks)
        ->assertCountTableRecords(10);

    $tasks->each(
        fn (Task $task) => $component
            ->assertTableColumnStateSet(
                'title',
                $task->title,
                $task
            )
            ->assertTableColumnFormattedStateSet(
                'status',
                str($task->status->value)->title()->headline(),
                $task
            )
            ->assertTableColumnStateSet(
                'due',
                $task->due,
                $task
            )
            ->assertTableColumnStateSet(
                'assignedTo.name',
                $task->assignedTo->name,
                $task
            )
            ->assertTableColumnStateSet(
                'concern.display_name',
                $task->concern->full_name,
                $task
            )
    );
});

// TODO: More tasks based on different Task states

// TODO: Sorting and Searching tests

// Permission Tests

test('ListTasks is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            TaskResource::getUrl('index')
        )->assertForbidden();

    $user->givePermissionTo('task.view-any');

    actingAs($user)
        ->get(
            TaskResource::getUrl('index')
        )->assertSuccessful();
});

// TODO: Test that mark_as_in_progress is visible to the proper users
//test('mark_as_in_progress is only visible to those with the proper access', function () {});

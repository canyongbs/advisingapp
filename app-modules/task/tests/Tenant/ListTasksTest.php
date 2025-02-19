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

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Task\Enums\TaskStatus;
use AdvisingApp\Task\Filament\Resources\TaskResource;
use AdvisingApp\Task\Filament\Resources\TaskResource\Pages\ListTasks;
use AdvisingApp\Task\Models\Task;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

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

    $component = livewire(ListTasks::class)
        ->set('tableRecordsPerPage', 10);

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
    $user = User::factory()->licensed(LicenseType::cases())->create();

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

test('Task status change from pending to in progress', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('task.*.update');
    $user->givePermissionTo('task.view-any');
    $user->givePermissionTo('task.*.view');

    actingAs($user);

    $task = Task::factory()->create([
        'status' => TaskStatus::Pending,
    ]);

    livewire(ListTasks::class)
        ->callTableAction('view.mark_as_in_progress', $task);

    $task->refresh();
    expect($task->status)->toEqual(TaskStatus::InProgress);
});

test('Task status change from pending to canceled', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('task.*.update');
    $user->givePermissionTo('task.view-any');
    $user->givePermissionTo('task.*.view');

    actingAs($user);

    $task = Task::factory()->create([
        'status' => TaskStatus::Pending,
    ]);

    livewire(ListTasks::class)
        ->callTableAction('view.mark_as_canceled', $task);

    $task->refresh();
    expect($task->status)->toEqual(TaskStatus::Canceled);
});

test('Task status change from in progress to completed', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('task.*.update');
    $user->givePermissionTo('task.view-any');
    $user->givePermissionTo('task.*.view');

    actingAs($user);

    $task = Task::factory()->create([
        'status' => TaskStatus::InProgress,
    ]);

    livewire(ListTasks::class)
        ->callTableAction('view.mark_as_completed', $task);

    $task->refresh();
    expect($task->status)->toEqual(TaskStatus::Completed);
});

test('Task status change from in progress to canceled', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('task.*.update');
    $user->givePermissionTo('task.view-any');
    $user->givePermissionTo('task.*.view');

    actingAs($user);

    $task = Task::factory()->create([
        'status' => TaskStatus::InProgress,
    ]);

    livewire(ListTasks::class)
        ->callTableAction('view.mark_as_canceled', $task);

    $task->refresh();
    expect($task->status)->toEqual(TaskStatus::Canceled);
});

test('Task status change from canceled to in progress', function () {
    $user = User::factory()->licensed(LicenseType::cases())->create();

    $user->givePermissionTo('task.*.update');
    $user->givePermissionTo('task.view-any');
    $user->givePermissionTo('task.*.view');

    actingAs($user);

    $task = Task::factory()->create([
        'status' => TaskStatus::Canceled,
    ]);

    livewire(ListTasks::class)
        ->removeTableFilters()
        ->callTableAction('view.mark_as_in_progress', $task);

    $task->refresh();
    expect($task->status)->toEqual(TaskStatus::InProgress);
});

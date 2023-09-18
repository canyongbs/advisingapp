<?php

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
                'status' => TaskStatus::IN_PROGRESS->value,
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
                'description',
                $task->description,
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

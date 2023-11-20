<?php

use App\Models\User;
use Assist\Task\Models\Task;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use Assist\Task\Filament\Resources\TaskResource;
use Assist\Task\Tests\RequestFactories\EditTaskRequestFactory;

// TODO: Write EditTask page tests
//test('A successful action on the EditTask page', function () {});
//
//test('EditTask requires valid data', function ($data, $errors) {})->with([]);

// Permission Tests

test('EditTask is gated with proper access control', function () {
    $user = User::factory()->create();

    $task = Task::factory()->create();

    actingAs($user)
        ->get(
            TaskResource::getUrl('edit', [
                'record' => $task,
            ])
        )->assertForbidden();

    livewire(TaskResource\Pages\EditTask::class, [
        'record' => $task->getRouteKey(),
    ])
        ->assertForbidden();

    $user->givePermissionTo('task.view-any');
    $user->givePermissionTo("task.{$task->id}.update");

    actingAs($user)
        ->get(
            TaskResource::getUrl('edit', [
                'record' => $task,
            ])
        )->assertSuccessful();

    // TODO: Finish these tests to ensure changes are allowed
    $request = collect(EditTaskRequestFactory::new()->create());

    livewire(TaskResource\Pages\EditTask::class, [
        'record' => $task->getRouteKey(),
    ])
        ->fillForm($request->toArray())
        ->call('save')
        ->assertHasNoFormErrors();

    // TODO: Check for changes
});

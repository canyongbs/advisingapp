<?php

use App\Models\User;
use Assist\Task\Models\Task;
use Assist\Authorization\Models\Permission;
use Illuminate\Support\Facades\Notification;
use Assist\Task\Notifications\TaskAssignedToUser;

beforeEach(function () {
    Notification::fake();
});

it('creates the proper permissions record when a Task is created', function () {
    $task = Task::factory()->create();

    expect(Permission::where('name', "task.{$task->id}.edit")->exists())->toBeTrue();
});

it('gives the proper permission to the creator of a Task', function () {
    /** @var Task $task */
    $task = Task::factory()->create();

    expect($task->createdBy->can("task.{$task->id}.edit"))->toBeTrue();
});

it('gives the proper permission to the assigned User of a Task on create and update', function () {
    /** @var Task $task */
    $task = Task::factory()->assigned()->create();

    expect($task->createdBy->can("task.{$task->id}.edit"))->toBeTrue()
        ->and($task->assignedTo->can("task.{$task->id}.edit"))->toBeTrue();

    $originalAssignedUser = $task->assignedTo;

    $newAssignedUser = User::factory()->create();

    $task->assignedTo()->associate($newAssignedUser)->save();

    $task->refresh();
    $originalAssignedUser->refresh();
    $newAssignedUser->refresh();

    expect($task->createdBy->can("task.{$task->id}.edit"))->toBeTrue()
        ->and($newAssignedUser->can("task.{$task->id}.edit"))->toBeTrue()
        ->and($originalAssignedUser->can("task.{$task->id}.edit"))->toBeFalse();
});

it('sends the proper notification to the assigned User', function () {
    $task = Task::factory()->assigned()->create();

    Notification::assertSentTo($task->assignedTo, TaskAssignedToUser::class);

    // Reset the fake notification store
    Notification::fake();

    $originalAssignedUser = $task->assignedTo;

    $newAssignedUser = User::factory()->create();

    $task->assignedTo()->associate($newAssignedUser)->save();

    Notification::assertSentTo($newAssignedUser, TaskAssignedToUser::class);
    Notification::assertNotSentTo($originalAssignedUser, TaskAssignedToUser::class);
});

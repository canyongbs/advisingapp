<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Livewire\livewire;

use Assist\Task\Filament\Resources\TaskResource;

// TODO: Write CreateTask page tests
//test('A successful action on the CreateTask page', function () {});
//
//test('CreateTask requires valid data', function ($data, $errors) {})->with([]);

// Permission Tests

test('CreateTask is gated with proper access control', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(
            TaskResource::getUrl('create')
        )->assertForbidden();

    livewire(TaskResource\Pages\CreateTask::class)
        ->assertForbidden();

    $user->givePermissionTo('task.view-any');
    $user->givePermissionTo('task.create');

    actingAs($user)
        ->get(
            TaskResource::getUrl('create')
        )->assertSuccessful();
});

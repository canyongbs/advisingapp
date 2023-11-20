<?php

namespace Assist\Task\Policies;

use App\Models\User;
use Assist\Task\Models\Task;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'task.view-any',
            denyResponse: 'You do not have permission to view tasks.'
        );
    }

    public function view(User $user, Task $task): Response
    {
        return $user->canOrElse(
            abilities: ['task.*.view', "task.{$task->id}.view"],
            denyResponse: 'You do not have permission to view this task.'
        );
    }

    public function create(User $user): Response
    {
        return $user->canOrElse(
            abilities: 'task.create',
            denyResponse: 'You do not have permission to create tasks.'
        );
    }

    public function update(User $user, Task $task): Response
    {
        return $user->canOrElse(
            abilities: ['task.*.update', "task.{$task->id}.update"],
            denyResponse: 'You do not have permission to update this task.'
        );
    }

    public function delete(User $user, Task $task): Response
    {
        return $user->canOrElse(
            abilities: ['task.*.delete', "task.{$task->id}.delete"],
            denyResponse: 'You do not have permission to delete this task.'
        );
    }

    public function restore(User $user, Task $task): Response
    {
        return $user->canOrElse(
            abilities: ['task.*.restore', "task.{$task->id}.restore"],
            denyResponse: 'You do not have permission to restore this task.'
        );
    }

    public function forceDelete(User $user, Task $task): Response
    {
        return $user->canOrElse(
            abilities: ['task.*.force-delete', "task.{$task->id}.force-delete"],
            denyResponse: 'You do not have permission to permanently delete this task.'
        );
    }
}

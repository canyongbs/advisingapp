<?php

namespace Assist\Task\Policies;

use App\Models\User;
use Assist\Task\Models\Task;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    public function viewAny(User $user): Response
    {
        return $user->can('task.view-any')
            ? Response::allow()
            : Response::deny('You do not have permission to view tasks.');
    }

    public function view(User $user, Task $task): Response
    {
        return $user->can('task.*.view') || $user->can("task.{$task->id}.view")
            ? Response::allow()
            : Response::deny('You do not have permission to view this task.');
    }

    public function create(User $user): Response
    {
        return $user->can('task.create')
            ? Response::allow()
            : Response::deny('You do not have permission to create tasks.');
    }

    public function update(User $user, Task $task): Response
    {
        return $user->can('task.*.update') || $user->can("task.{$task->id}.update")
            ? Response::allow()
            : Response::deny('You do not have permission to update this task.');
    }

    public function delete(User $user, Task $task): Response
    {
        return $user->can('task.*.delete') || $user->can("task.{$task->id}.delete")
            ? Response::allow()
            : Response::deny('You do not have permission to delete this task.');
    }

    public function restore(User $user, Task $task): Response
    {
        return $user->can('task.*.restore') || $user->can("task.{$task->id}.restore")
            ? Response::allow()
            : Response::deny('You do not have permission to restore this task.');
    }

    public function forceDelete(User $user, Task $task): Response
    {
        return $user->can('task.*.force-delete') || $user->can("task.{$task->id}.force-delete")
            ? Response::allow()
            : Response::deny('You do not have permission to permanently delete this task.');
    }
}

<?php

namespace Assist\Task\Observers;

use Assist\Task\Models\Task;
use Illuminate\Support\Facades\DB;
use Assist\Authorization\Models\Permission;

class TaskObserver
{
    public function saving(Task $task): void
    {
        DB::beginTransaction();

        if (is_null($task->created_by)) {
            $task->created_by = auth()->id();
        }

        if (is_null($task->assigned_to)) {
            $task->assigned_to = auth()->id();
        }

        //if ($task->isDirty('assigned_to')) {
        //    ray('here');
        //}
    }

    public function creating(Task $task)
    {
        Permission::create([
            'name' => "task.{$task->id}.edit",
            'guard_name' => 'web',
        ]);
    }

    public function created(Task $task): void
    {
        // Add permissions to creator
        $task->createdBy->givePermissionTo("task.{$task->id}.edit");

        // Add permissions to assigned User unless they are the creator
        if ($task->assigned_to !== $task->created_by) {
            $task->assignedTo?->givePermissionTo("task.{$task->id}.edit");
        }
    }

    public function updated(Task $task): void
    {
        // Remove permissions from previously assigned User unless they are the creator

        // Add permissions to newly assigned User unless they are the creator
    }

    public function saved(Task $task): void
    {
        DB::commit();
    }

    public function deleted(Task $task): void
    {
        // Remove permissions from creator and assigned User
    }
}

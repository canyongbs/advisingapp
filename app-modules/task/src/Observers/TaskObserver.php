<?php

namespace Assist\Task\Observers;

use Exception;
use App\Models\User;
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
    }

    public function creating(Task $task): void
    {
        Permission::create([
            'name' => "task.{$task->id}.edit",
            'guard_name' => 'web',
        ]);
    }

    public function created(Task $task): void
    {
        try {
            // Add permissions to creator
            $task->createdBy->givePermissionTo("task.{$task->id}.edit");

            // Add permissions to assigned User unless they are the creator
            if ($task->assigned_to !== $task->created_by) {
                $task->assignedTo?->givePermissionTo("task.{$task->id}.edit");
            }
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    public function updated(Task $task): void
    {
        try {
            if ($task->isDirty('assigned_to') && $task->assigned_to !== $task->created_by) {
                // Remove permissions from previously assigned User unless they are the creator
                User::find($task->getOriginal('assigned_to'))?->revokePermissionTo("task.{$task->id}.edit");

                // Add permissions to newly assigned User unless they are the creator
                $task->assignedTo?->givePermissionTo("task.{$task->id}.edit");
            }
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }
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

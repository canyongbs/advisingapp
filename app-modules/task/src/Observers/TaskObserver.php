<?php

namespace Assist\Task\Observers;

use Exception;
use App\Models\User;
use Assist\Task\Models\Task;
use Illuminate\Support\Facades\DB;
use Assist\Authorization\Models\Permission;
use Assist\Notifications\Events\TriggeredAutoSubscription;
use Assist\Task\Notifications\TaskAssignedToUserNotification;

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
            'name' => "task.{$task->id}.update",
            'guard_name' => 'web',
        ]);
    }

    public function created(Task $task): void
    {
        try {
            // Add permissions to creator
            $task->createdBy?->givePermissionTo("task.{$task->id}.update");

            // Add permissions to assigned User unless they are the creator
            if ($task->assigned_to !== $task->created_by) {
                $task->assignedTo?->givePermissionTo("task.{$task->id}.update");
            }

            TriggeredAutoSubscription::dispatchIf(! empty($task->createdBy), $task->createdBy, $task);
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    public function updated(Task $task): void
    {
        try {
            if ($task->isDirty('assigned_to') && $task->assigned_to !== $task->created_by) {
                if ($task->getOriginal('assigned_to') !== $task->created_by) {
                    User::find($task->getOriginal('assigned_to'))?->revokePermissionTo("task.{$task->id}.update");
                }

                // Add permissions to newly assigned User
                $task->assignedTo?->givePermissionTo("task.{$task->id}.update");
            }
        } catch (Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    public function saved(Task $task): void
    {
        DB::commit();

        if (! empty($task->assignedTo) && ($task->wasChanged('assigned_to') || ($task->wasRecentlyCreated))) {
            $task->assignedTo->notify(new TaskAssignedToUserNotification($task));

            TriggeredAutoSubscription::dispatch($task->assignedTo, $task);
        }
    }

    public function deleted(Task $task): void
    {
        // Remove permissions from creator and assigned User
    }
}

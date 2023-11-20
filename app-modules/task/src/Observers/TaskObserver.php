<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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

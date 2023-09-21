<?php

namespace Assist\Task\Actions;

use Assist\Task\Models\Task;
use Illuminate\Database\Eloquent\Model;

class UpdateTask
{
    public function handle(Task|Model $task, array $data): Task
    {
        $data = collect($data);

        /** @var Task $record */
        $task->fill($data->except('assigned_to')->toArray());

        $task->assigned_to = $data->get('assigned_to');

        $task->save();

        return $task;
    }
}

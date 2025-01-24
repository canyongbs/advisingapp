<?php

namespace App\Health\Checks;

use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;
use Spatie\Multitenancy\Landlord;
use Spatie\ScheduleMonitor\Support\ScheduledTasks\ScheduledTasks;
use Spatie\ScheduleMonitor\Support\ScheduledTasks\Tasks\Task;

class ScheduleMonitorCheck extends Check
{
    public function run(): Result
    {
        return Landlord::execute(function () {
            $tasks = ScheduledTasks::createForSchedule()->monitoredTasks();

            $lateTasks = $tasks->filter(fn (Task $task) => $task->lastRunFinishedTooLate());

            $failedTasks = $tasks->filter(fn (Task $task) => $task->lastRunFailed());

            $result = Result::make();

            if ($lateTasks->isNotEmpty() || $failedTasks->isNotEmpty()) {
                return $result->shortSummary("Late Tasks: {$lateTasks->count()} | Failed Tasks: {$failedTasks->count()}")
                    ->meta([
                        'late_tasks' => $lateTasks->map(fn (Task $task) => $task->name())->toArray(),
                        'failed_tasks' => $failedTasks->map(fn (Task $task) => $task->name())->toArray(),
                    ])
                    ->failed();
            }

            return $result->ok();
        });
    }
}

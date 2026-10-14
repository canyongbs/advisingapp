# Upstream fix: `cboxdk/laravel-queue-autoscale` drops worker output while the manager shuts down

- **Package:** `cboxdk/laravel-queue-autoscale`
- **Version observed:** `4.3.1`
- **Repository:** https://github.com/cboxdk/laravel-queue-autoscale
- **Affected code:** `Manager\AutoscaleManager::shutdown()` and `Workers\WorkerTerminator::terminateAll()`.
- PR Opened: https://github.com/cboxdk/laravel-queue-autoscale/pull/77 (combined with the dead worker exit status fix)

## Environment where reproduced

- Laravel `13.30.1`, PHP `8.4`, on Amazon ECS Fargate worker tasks with s6-overlay supervising `queue:autoscale`.
- Queue: Amazon SQS standard queues, 1200s visibility timeout. Cluster mode on Redis Cluster.
- Workers are stopped for a deployment or scale-in by stopping the manager (`s6-svc -d`, which sends it SIGTERM), so they finish their current job and take no more.

## Summary

While the manager runs, every cycle calls `processWorkerOutput()`. It drains each worker's stdout (to the renderer) and stderr (to the log channel as `[worker <pid>] <line>`).

Once the manager receives a stop signal, `run()` leaves the loop and calls `shutdown()`. That SIGTERMs every worker through `WorkerTerminator::terminateAll()` and then polls `isRunning()` until they all exit or the shutdown deadline passes. That deadline is the longest `workers.shutdown_timeout_seconds` in the pool, up to 1200 seconds here. Nothing reads worker output during that wait or after it:

- Symfony's `Process::isRunning()` keeps reading the pipes into the process's own buffer, so workers never block on a full pipe. But nothing collects that buffer, and it is discarded when the manager exits.
- So everything a worker writes after the stop signal is lost. That includes `queue:work`'s `DONE` / `FAIL` lines, the job's own log lines on a stderr channel, exceptions, and memory warnings.
- The same applies to how each worker exited. `cleanupDeadWorkers()` is not called during shutdown, so a worker that crashes or is OOM-killed while finishing its last job leaves no trace.

The window is exactly when the jobs most worth seeing are running: the long ones a graceful stop was meant to protect.

## How it showed up

Our worker tasks drain before a deployment or scale-in replaces them: the manager is stopped, its workers finish their current jobs, and the task is then released for ECS to stop. In dev testing, the jobs a draining task was still running (load-test jobs that sleep 120 to 1100 seconds and log when they start and finish) logged `Load test job started` and a `RUNNING` line, but never a `Load test job finished` or `DONE` line.

The jobs did complete:

- the workers ran until the jobs' expected end times, then exited;
- the task was released two to three seconds later;
- none of the jobs was delivered a second time.

Only the output was lost. For example, on one task the manager logged `Shutting down autoscale manager, terminating all workers` at 01:23:22. It was running three 570-second jobs and a 120-second job. The 120-second job ended at about 01:23:33 and the others at 01:32:19, and nothing from any of those workers reached the log after 01:23:22.

Without those lines there is no way to confirm from the logs that a drained task's jobs finished. A job that failed during a drain would also be invisible apart from its record in the failed-jobs table.

## Suggested fix

Keep draining worker output while waiting for workers to stop, and once more after they have all exited.

1. Give `WorkerTerminator::terminateAll()` an optional callback that runs on each poll of the wait loop:

    ```php
    public function terminateAll(iterable $workers, ?callable $onTerminate = null, ?callable $whileWaiting = null): int
    {
        // ... signal every worker as today ...

        while ($pending !== [] && time() < $deadline) {
            foreach ($pending as $pid => $worker) {
                if (! $worker->isRunning()) {
                    unset($pending[$pid]);
                }
            }

            if ($whileWaiting !== null) {
                $whileWaiting();
            }

            if ($pending !== []) {
                usleep(100_000);
            }
        }

        // ... SIGKILL stragglers as today ...
    }
    ```

2. In `AutoscaleManager::shutdown()`, pass `fn () => $this->processWorkerOutput()` as that callback, and call `processWorkerOutput()` once more after `terminateAll()` returns. `WorkerOutputBuffer` already drains exited workers (Symfony keeps their output after exit), so the final call picks up the last lines of every worker, including any that had to be SIGKILLed.

Notes for the implementation:

- Throttle the callback, for example to once a second. The wait loop polls every 100 ms, and draining every worker's pipes that often is wasted work.
- Pending partial lines in `WorkerOutputBuffer` should be flushed at the end, so a final line with no trailing newline is not lost.
- Optional, related to [cbox-queue-autoscale-dead-worker-exit-status.md](cbox-queue-autoscale-dead-worker-exit-status.md): log each worker's exit status as it leaves `$pending`. The "how did the worker exit" log line would then cover a shutdown too, not only a normal cycle.

## Tests

- A worker that writes to stderr after the manager receives SIGTERM has that line logged as `[worker <pid>] <line>` before `shutdown()` returns.
- A worker that writes its last line and exits during the shutdown wait has that line logged.
- A worker that writes a line with no trailing newline just before exiting still has the line logged.
- Output is still forwarded for a worker that has to be SIGKILLed at the shutdown deadline, up to the point it was killed.

---

## Consuming-app cleanup (after this ships)

> **Implementing the upstream fix? Ignore this section.** It lists Advising App changes to make **after** the fix is released and we upgrade. They are not part of the package change.

Advising App has no override for this, so there is nothing to delete. Once a release forwards worker output during shutdown:

1. Bump the exact pin of `cboxdk/laravel-queue-autoscale` in `composer.json`, then run `pls exec app composer update cboxdk/laravel-queue-autoscale`.
2. Check on dev: dispatch a long job (`php artisan queue:loadtest AdvisingApp 1 --duration=300` through ECS Exec), drain the task that picks it up (`php artisan ecs:worker-drain`), and confirm that `Load test job finished` and the job's `DONE` line appear before `Worker task drained and released its scale-in protection`.
3. The worker drain test plan (`docker/devops/ecs/advisingapp/deploy-worker-drain.md`, scenario M2) expects a draining task to log `Load test job finished` for its long job. Until this ships, that check has to be made from job timings and the absence of redelivered jobs instead. Once it ships, no change is needed.

### Then

Delete this document.

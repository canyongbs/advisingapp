# Upstream fix — `cboxdk/laravel-queue-autoscale`: log how a dead worker exited

- **Package:** `cboxdk/laravel-queue-autoscale`
- **Version observed:** `4.3.1`
- **Repository:** https://github.com/cboxdk/laravel-queue-autoscale
- **Affected code:** `Workers\WorkerScaler::cleanupDeadWorkers()`, called every manager cycle from `Manager\AutoscaleManager`.
- PR Opened: _not yet_

## Environment where reproduced

- Laravel `13.30.1`, PHP `8.4`, on Amazon ECS Fargate (4 vCPU / 8 GB worker tasks) with s6-overlay supervising `queue:autoscale`.
- Queue: Amazon SQS standard queues, 1200s visibility timeout. Cluster mode on Redis Cluster.

## Summary

When the manager finds a worker process that is no longer running, it removes it from the pool and logs only:

```
Removed dead worker {"pid":12345}
```

That line is the same whether the worker was stopped on request or killed. It records no exit code, no termination signal, and nothing about whether the manager had asked the worker to stop. So a worker that crashed or was killed from outside PHP is indistinguishable from a normal scale-down.

The information is available at that point. `WorkerProcess::$process` is the Symfony `Process`, which has already been reaped by `isRunning()`, so `getExitCode()`, `hasBeenSignaled()` and `getTermSignal()` all answer. `WorkerProcess::isTerminating()` says whether the manager requested the stop.

## How it showed up

In production, a busy worker task lost 138 workers in under six minutes that the manager never asked to stop. None printed `queue:work`'s `Worker STOPPED ...` line, none wrote to stderr (which the manager forwards), and none raised an exception. Any job those workers were running stayed invisible in SQS for the full visibility timeout and then failed with `MaxAttemptsExceededException`.

The only trace in the logs was `Removed dead worker`, interleaved with hundreds of identical lines from normal scale-downs. Telling the two apart meant matching each PID against an earlier `Worker termination requested` line by hand, and the cause (a signal? which one? a fatal exit?) was still unknowable.

## Suggested fix

In `cleanupDeadWorkers()`, add the exit status to the existing log line:

```php
$process = $worker->process;
$wasSignaled = $process->hasBeenSignaled();

Log::channel(AutoscaleConfiguration::logChannel())->warning('Removed dead worker', [
    'pid' => $worker->pid(),
    'connection' => $worker->connection,
    'queue' => $worker->queue,
    'group' => $worker->group,
    'exit_code' => $process->getExitCode(),
    'term_signal' => $wasSignaled ? $process->getTermSignal() : null,
    'termination_requested' => $worker->isTerminating(),
    'uptime_seconds' => $worker->uptimeSeconds(),
]);
```

Notes for the implementation:

- Only call `getTermSignal()` when `hasBeenSignaled()` is true. It throws on PHP builds with `--enable-sigchild` when no signal is known.
- Consider logging requested, clean exits (`termination_requested` and exit code `0`) at `info` or `debug` instead of `warning`. They are the normal scale-down path and far outnumber the rest; keeping the unexpected exits at `warning` makes them stand out.
- A `WorkerExited` event carrying the same fields would let applications alert on it without parsing logs. Optional.

## Tests

- A worker that exits on its own with a non-zero code is logged with that `exit_code`, `term_signal` `null` and `termination_requested` `false`.
- A worker killed with `SIGKILL` is logged with `term_signal` `9`.
- A worker whose termination was requested and that exited with `0` is logged as a requested, clean exit.

---

## Consuming-app cleanup (after this ships)

> **Implementing the upstream fix? Ignore this section.** It lists Advising App changes to make **after** the fix is released and we upgrade. They are not part of the package change.

Advising App logs the exit status through an override: it swaps the package's `WorkerSpawner` for one that wraps each spawned worker in a subclass that logs `Worker exited unexpectedly` when the manager finds it dead.

Once a release logs the exit status itself:

1. Bump the exact pin of `cboxdk/laravel-queue-autoscale` in `composer.json`, then run `pls exec app composer update cboxdk/laravel-queue-autoscale`.
2. Delete `app/Overrides/QueueAutoscale/ExitReportingWorkerProcess.php` and `app/Overrides/QueueAutoscale/ExitReportingWorkerSpawner.php`, and the now-empty `app/Overrides/QueueAutoscale` directory.
3. In `App\Providers\QueueObservabilityServiceProvider::register()`, remove the `$this->app->extend(WorkerSpawner::class, ...)` call and the comment above it, and the `ExitReportingWorkerSpawner` and `WorkerSpawner` imports.
4. Update the tests:
    - Delete `tests/Landlord/Overrides/QueueAutoscale/ExitReportingWorkerProcessTest.php` and `tests/Landlord/Overrides/QueueAutoscale/ExitReportingWorkerSpawnerTest.php`, and the now-empty directory.
    - In `tests/Landlord/Providers/QueueObservabilityServiceProviderTest.php`, remove "replaces the autoscale worker spawner with one whose workers report how they exit" and the `ExitReportingWorkerSpawner` and `WorkerSpawner` imports.
5. Our override logs `Worker exited unexpectedly`; upstream will likely enrich `Removed dead worker` instead. Update any CloudWatch Logs Insights queries, metric filters or alarms that match on `Worker exited unexpectedly` to the upstream message and fields.
6. Check on dev that killing a worker by hand (`kill -9 <pid>` through ECS Exec) produces the upstream log line with its exit code and signal.

### Then

Delete this document.

# Upstream fixes: `cboxdk/laravel-queue-autoscale` memory capacity (worker footprint, booting workers, headroom scope, OOM kills)

This document tracks contributions to how the autoscaler decides how many workers a host can hold in memory. Together they let it over-pack hosts until the kernel OOM-killed workers.

- **Part 1:** the measured per-worker memory cost misses a worker's boot peak, and always wins over configured estimates. PR Opened: _not yet_
- **Part 2:** capacity is read from memory in use right now, so workers that are still booting look free, and a batch of them overshoots. PR Opened: _not yet_
- **Part 3:** the memory limit and used percentage come from different scopes, so in a container without its own memory limit the autoscaler sizes against the whole VM. This was the root cause. PR Opened: _not yet_
- **Part 4:** nothing reacts to the kernel OOM-killing workers. PR Opened: _not yet_

Part 3 is the one that caused the incident and the one Advising App overrides; see [Consuming-app cleanup](#consuming-app-cleanup-after-these-ship). Parts 1, 2 and 4 make capacity safer near the limit and are independent improvements.

## Environment where reproduced

- Laravel `13.30.1`, PHP `8.4` with OPcache enabled for the CLI (`opcache.enable_cli=1`) and, at first, the tracing JIT (64 MB buffer).
- Amazon ECS Fargate worker tasks, 4 vCPU / 8 GB (also seen at 2 vCPU / 4 GB), with no container-level memory limit (the limit was the task's). Cgroup v1. Cluster mode on Redis Cluster.
- Packages: `cboxdk/laravel-queue-autoscale` `4.3.1`, `cboxdk/laravel-queue-metrics` `3.4.0`, `cboxdk/system-metrics` `3.0.2`.

## How it showed up

Under a load test, one worker task settled at a hard ceiling of 43 workers (22 on the earlier 4 GB tasks). The cluster leader kept assigning 51. Every cycle the manager spawned 8 more, the kernel SIGKILLed workers (mostly the newest, at their boot peak), the manager saw memory freed and spawned again. 407 workers were killed in six minutes. Every job a killed worker held stayed invisible in SQS for the visibility timeout and then failed with `MaxAttemptsExceededException`.

What each worker actually costs, measured with `/proc/<pid>/status` on an idle `queue:work` (before any job):

|             | Private (`RssAnon`) | OPcache segment (`RssShmem`) | Total   |
| ----------- | ------------------- | ---------------------------- | ------- |
| OPcache off | ~110 MB             | 0                            | ~110 MB |
| OPcache on  | ~40 MB              | ~90 MB                       | ~130 MB |

With OPcache on in the CLI, every worker is a separate process with its own shared-memory segment, so that ~90 MB is per worker, not shared. A busy worker with the tracing JIT also fills up to its JIT buffer size on top.

None of this showed in AWS metrics: ECS `MemoryUtilization` and Container Insights `MemoryUtilized` subtract page cache, which includes shared memory, so the task read about 3.25 GB of 8 GB while it was being OOM-killed. That is an AWS monitoring gap, not a package bug, but it means the package cannot lean on the platform to notice.

Lowering `limits.max_memory_percent` from 85 to 70 and turning off the JIT changed nothing: the same task held the same ~44-worker ceiling. Reading what the autoscaler sees inside a running worker (ECS Exec) explained why:

| Reading                                          | Value                                                                                    |
| ------------------------------------------------ | ---------------------------------------------------------------------------------------- |
| `/proc/meminfo` `MemTotal` (the Fargate microVM) | ~15.7 GB, for an 8 GB task                                                               |
| Container cgroup (v1) `memory.limit_in_bytes`    | unlimited; the 8 GB limit is on the task's parent cgroup, which the container cannot see |
| `SystemMetrics::limits()`                        | `source=cgroup_v1 limit_mb=15691`                                                        |
| `SystemMetrics::memory()->usedPercentage()`      | 7.9% at ~700 MB used                                                                     |

So the autoscaler sized every task against ~15.7 GB while the kernel enforced 8 GB, and `max_memory_percent` was a percentage of the wrong number.

---

## Part 1: include the boot peak, and let a configured estimate act as a floor

### Affected code

- `Scaling\MeasuredResourceCollector::collect()` derives `memoryMbPerWorker` from `QueueMetrics::getAllJobsWithMetrics()`, a per-job memory average recorded by `cboxdk/laravel-queue-metrics` (`Support\JobMetricsCollector`).
- `Scaling\ResourceEstimateResolver::resolve()` gives that measured value precedence over a per-queue `resources.memory_mb` and the global `limits.worker_memory_mb_estimate`.

### Summary

`JobMetricsCollector` records the worker process's peak RSS while the job ran, falling back to PHP's own peak heap usage when process metrics are unavailable. On Linux, RSS includes the resident pages of the worker's OPcache and JIT segments and its framework state, so the steady-state footprint is largely captured. Two gaps remain:

- It is only sampled while a job runs, so it misses the boot peak: a new worker compiles the whole app into its own OPcache before it takes its first job. That is when the newest workers were killed.
- The measured value always wins, so an operator who knows workers need ~250 MB cannot set that as a floor.

(RSS also counts shared library pages in full for every worker, which overstates the cost slightly; that errs safe.)

### Suggested fix

- Include the boot peak. The manager already knows every worker's PID; on Linux, `VmHWM` in `/proc/<pid>/status` is the process's peak RSS since it started, boot included. Use a high percentile (for example p90) of live workers' `VmHWM` alongside the per-job value, and expose the result in the capacity details. Where `/proc` is unavailable (macOS, BSD, Windows), keep today's behaviour.
- Treat a configured estimate as a floor: use `max(measured, configured)` rather than letting measurement replace it.

### Tests

- With fake `/proc/<pid>/status` files for three worker PIDs whose `VmHWM` exceeds the per-job value, the per-worker cost is their p90 `VmHWM`.
- A configured estimate above the measured value is used.
- Without `/proc`, behaviour is unchanged.

---

## Part 2: count booting workers at full cost

### Affected code

- `Scaling\Calculators\CapacityCalculator::calculateMaxWorkers()`: `additional = free × (max_memory_percent − used%) / memoryMbPerWorker`, evaluated on memory in use right now.

### Summary

A worker spawned a few seconds ago has not yet grown to its footprint (it is still compiling the app into its OPcache), so the next cycle sees memory it will soon use as free. Spawning several workers per cycle overshoots the limit before any of them is fully booted. This produced the 43 → 51 → killed → 43 loop above.

### Suggested fix

Either of:

- Charge every worker younger than a settle time (for example 30s) at the full per-worker cost instead of its current usage.
- Base capacity on counts rather than instantaneous free memory: `max workers = (limit × max_memory_percent − manager and baseline usage) / per-worker cost`.

Both are OS-independent.

### Tests

- With 8 workers spawned in the last cycle at a fraction of their footprint, capacity does not allow another batch that would exceed the limit once they settle.

---

## Part 3: measure headroom in one scope, once

### Affected code

- `CapacityCalculator::refreshSystemMetrics()`: the total is `SystemMetrics::limits()->availableMemoryBytes()`, while the used percentage is `SystemMetrics::memory()->usedPercentage()`.
- `cboxdk/system-metrics` `Sources\SystemLimits\CompositeSystemLimitsSource::readFromCgroup()`: when a cgroup exists but has no memory limit, it pairs the host's total memory with the cgroup's usage.

### Summary

- `SystemMetrics::memory()` reads `/proc/meminfo` on Linux, which is host or VM-wide, while `limits()` prefers the container's cgroup. The two can describe different scopes.
- When the container's cgroup has no memory limit, `limits()` falls back to `/proc/meminfo` for the total too. On Amazon ECS Fargate the limit is set on the task's parent cgroup, invisible from inside the container, and `/proc/meminfo` describes a microVM about twice the task's size. The autoscaler therefore sizes against roughly double the real limit. The same happens wherever a parent cgroup carries the limit (Kubernetes pod-level limits, systemd slices).
- `availableMemoryBytes()` is the limit minus current usage, i.e. free memory, but `calculateMaxWorkers()` multiplies it by the remaining percentage as if it were the total, so usage is subtracted twice. This errs conservative, but it makes the result hard to reason about.
- On a systemd host, a cgroup is always present but usually unlimited; pairing the host total with only that service's usage leaves out every other process on the machine.

### Suggested fix

Headroom is the smaller of:

- the cgroup's spare memory (`memory.max` or `memory.limit_in_bytes`, minus usage), only when a cgroup memory limit is actually set, with usage counted as Kubernetes counts its eviction working set (usage minus `inactive_file`);
- the host's `MemAvailable`.

Use that one headroom figure, not a total times a percentage, and take the used percentage from the same scope. This is correct on bare metal, VMs, Docker, Kubernetes, ECS and systemd. `max_memory_percent` then applies to the limit used for the headroom.

A process cannot see a limit set on a parent cgroup, so also:

- document that containers need their own memory limit (on ECS, the container definition's `memory`), not only a task or pod limit;
- optionally accept an explicit limit in configuration (for example `limits.memory_limit_mb`) for platforms where that is not possible.

### Tests

- Containerized with a limit: headroom and used percentage follow the cgroup.
- Cgroup present without a limit: headroom follows the host's `MemAvailable`.
- An explicit configured limit wins over both.
- No double subtraction: at 50% usage and a 70% ceiling, 20% of the limit is available.

---

## Part 4: back off after OOM kills

### Affected code

- The manager's evaluation cycle. `cboxdk/system-metrics` already parses the cgroup's OOM kill counter (`Support\Parser\Cgroup\V2\CgroupV2MemoryParser::parseOomKills()`, exposed as `ContainerLimits::$oomKillCount`).

### Summary

When the kernel OOM-kills workers, the manager only sees dead workers and freed memory, and spawns again.

### Suggested fix

Track `oomKillCount` between cycles. When it rises, lower this host's worker ceiling to the current count for a cooldown period and log a warning naming the cause.

Only enter the cooldown when the counter confirms an OOM kill. A worker dying from SIGKILL without a termination request is not enough on its own: `queue:work` also SIGKILLs itself when a job exceeds `--timeout`, and an ECS or operator kill has the same exit status. Where the counter is not readable, log such exits as SIGKILLs of unknown cause (see `cbox-queue-autoscale-dead-worker-exit-status.md`) rather than reducing capacity.

### Tests

- With the OOM counter rising between two cycles, the host's capacity is held at its current worker count for the cooldown.
- An unrequested SIGKILL with the counter unchanged does not reduce capacity.

---

## Consuming-app cleanup (after these ship)

> **Implementing the upstream fixes? Ignore this section.** It lists Advising App changes to make **after** the fixes are released and we upgrade. They are not part of the package changes.

Advising App works around Part 3 in two places:

- Worker task definitions (devops submodule): the `worker` container has its own `"memory"` limit, equal to the task's, so the container's cgroup shows the real limit. **This is permanent**: no package can see a limit set only on the task's parent cgroup.
- `App\Overrides\SystemMetrics\ContainerMemoryMetricsSource`, registered with `SystemMetricsConfig::setMemoryMetricsSource()` in `App\Providers\QueueObservabilityServiceProvider::register()`. It makes `SystemMetrics::memory()` report the container's cgroup usage and limit when one is set, so the used percentage is of the same limit as the capacity.

Once Part 3 ships (Parts 1, 2 and 4 need no Advising App changes):

1. Bump the exact pin of `cboxdk/laravel-queue-autoscale` in `composer.json`, then run `pls exec app composer update cboxdk/laravel-queue-autoscale`. If the fix is in `cboxdk/system-metrics`, update that as well.
2. Delete `app/Overrides/SystemMetrics/ContainerMemoryMetricsSource.php` and the now-empty `app/Overrides/SystemMetrics` directory.
3. In `App\Providers\QueueObservabilityServiceProvider::register()`, remove the `SystemMetricsConfig::setMemoryMetricsSource(...)` call and the `ContainerMemoryMetricsSource` and `SystemMetricsConfig` imports.
4. Update the tests:
    - Delete `tests/Landlord/Overrides/SystemMetrics/ContainerMemoryMetricsSourceTest.php` and the now-empty directory.
    - In `tests/Landlord/Providers/QueueObservabilityServiceProviderTest.php`, remove "measures memory against the container limit for the autoscaler" and the `ContainerMemoryMetricsSource` and `SystemMetricsConfig` imports.
5. Keep the worker container's `"memory"` limit in the task definitions.
6. Run the dev load test (`php artisan queue:loadtest AdvisingApp 3000 --duration=10` from a scheduler task) and confirm there are no `Worker exited unexpectedly` lines with `term_signal` `9` and no `MaxAttemptsExceededException` failures about 20 minutes later.

### Then

Delete this document.

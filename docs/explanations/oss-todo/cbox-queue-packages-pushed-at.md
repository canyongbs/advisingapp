# Upstream fixes — `cboxdk` queue packages: how `pushedAt` is used for job duration (queue-metrics) and pickup time (queue-autoscale)

This document tracks two related contributions. Both are about the payload's `pushedAt` timestamp.

- **Part 1 — `cboxdk/laravel-queue-metrics`:** job duration is measured from `pushedAt` (dispatch time), not from when processing started. PR Opened: _not yet_
- **Part 2 — `cboxdk/laravel-queue-autoscale`:** pickup time counts dispatch delays and earlier attempts as waiting, and the package never stamps `pushedAt` itself. PR Opened: _not yet_

Part 1 should land first, or together with Part 2. Part 2's optional `pushedAt` stamping would make Part 1's bug hit every user of the package.

## Environment where reproduced

- Laravel `13.30.1`, PHP `8.4`.
- Queue: Amazon SQS standard queues (production), Redis Cluster (local development). No Horizon.
- Packages: `cboxdk/laravel-queue-autoscale` `4.3.1`, `cboxdk/laravel-queue-metrics` `3.4.0`, `cboxdk/laravel-queue-monitor` `1.11.0`.

## Why Advising App stamps `pushedAt` in the first place

The queue autoscaler sizes workers from two inputs: **how many** jobs are waiting and **how long** they have been waiting. On SQS, Laravel cannot report the second one: `SqsQueue::creationTimeOfOldestPendingJob()` returns `null`, so the autoscaler sees an oldest-job age of `0` for every queue.

`cboxdk/laravel-queue-autoscale` has a second source for wait time: the **p95 pickup time**. `Cbox\LaravelQueueAutoscale\Pickup\PickupTimeRecorder` listens for `JobProcessing` and records `microtime(true) - $payload['pushedAt']`, which is how long the job waited before a worker picked it up. `HybridStrategy` uses that p95 in place of the oldest-job age.

Nothing writes `pushedAt` unless you use Horizon. Laravel's payload only has `createdAt`, in whole seconds, and none of the cbox packages set `pushedAt`. Without it, `PickupTimeRecorder` returns early and the p95 pickup signal never exists. The autoscaler then falls back to `BacklogDrainCalculator`'s "age unavailable" branch, `workers = backlog / (SLA / avgJobTime)`. That branch scales on queue depth alone. It has no SLA-progress urgency multiplier and raises no SLA-breach events.

So Advising App does two things:

- registers a payload hook that stamps every job with `pushedAt = microtime(true)` (`App\Providers\QueueServiceProvider`);
- moves `pushedAt` to the available-at time for delayed SQS jobs (`App\Queue\TenantFairSqsQueue::createPayload()`).

This turns on the pickup-time signal and gives the autoscaler a real wait-time input on SQS. It also exposes both problems below.

---

## Part 1 — `cboxdk/laravel-queue-metrics`: job duration is measured from `pushedAt`

### Target package

- **Package:** `cboxdk/laravel-queue-metrics`
- **Version observed:** `3.4.0`
- **Repository:** https://github.com/cboxdk/laravel-queue-metrics
- **Affected class:** `Cbox\LaravelQueueMetrics\Support\JobMetricsCollector::collect()` (used by `JobProcessedListener` and `JobFailedListener`)
- **Downstream consumer affected:** `cboxdk/laravel-queue-autoscale` (`4.3.1`) reads the resulting average duration as its "average job time".

### Summary

`JobMetricsCollector::collect()` computes a job's duration as `microtime(true) - $payload['pushedAt']`. `pushedAt` is the time the job was **dispatched**, not the time a worker **started** it. Horizon sets it at push time, and Advising App now does too. So the recorded "duration" is **time waiting in the queue + time running**.

Without `pushedAt` the collector falls back to `microtime(true)` at completion. The duration is then roughly `0 ms` for every job.

Neither value is the processing time the metric claims to be:

| `pushedAt` in payload                      | Recorded `durationMs` | What it really measures |
| ------------------------------------------ | --------------------- | ----------------------- |
| absent (plain Laravel, no Horizon)         | ~0                    | nothing                 |
| set at dispatch (Horizon, or Advising App) | wait time + run time  | end-to-end latency      |
| what the metric should be                  | run time              | processing duration     |

### Why it matters

The average duration feeds directly into worker sizing in `cboxdk/laravel-queue-autoscale`:

- `QueueMetricsAdapter` maps `avg_duration_ms` → `QueueMetricsData::$avgDuration` (seconds).
- `HybridStrategy::determineJobTime()` uses it as `avgJobTime` when it is between 10 ms and 600 s. Otherwise it uses `scaling.fallback_job_time_seconds` (default 2 s).
- `avgJobTime` is a multiplier in every worker calculation:
    - Little's Law steady state: `workers = arrivalRate × avgJobTime`.
    - `BacklogDrainCalculator`: `workers = backlog / (timeUntilBreach / avgJobTime)` (or `backlog / (SLA / avgJobTime)` when age is unavailable).

**When `pushedAt` is absent**, every duration is under 10 ms. The strategy rejects them and always uses the 2 s fallback, so real job durations never reach the autoscaler.

**When `pushedAt` is set at dispatch**, the duration includes queue wait. That creates a feedback loop:

1. A backlog builds, so jobs wait longer before pickup.
2. The wait is added to every job's "duration", so `avgJobTime` goes up.
3. A larger `avgJobTime` raises the target worker count from both calculators, on top of the backlog and pickup-time signals that already account for the wait.
4. The wait is counted twice, once as wait time and again inside "job time", so the autoscaler overshoots. The configured max workers caps it.

When the wait passes the 600 s sanity cap, the strategy drops the value and uses the 2 s fallback. The input jumps from very large to small just as the backlog is at its worst.

It also breaks other things that read the duration: baselines (`CalculateBaselinesAction`), anomaly detection (`BaselineDeviationService`), per-job-class averages, and the Prometheus and dashboard numbers. They all show end-to-end latency (or zero) labelled as "duration".

`cboxdk/laravel-queue-monitor` is **not** affected. It computes `duration_ms` from its own `started_at` / completion timestamps.

### Root cause

`src/Support/JobMetricsCollector.php`:

```php
public static function collect(string $jobId, array $payload): JobMetricsSnapshot
{
    $startTime = is_numeric($payload['pushedAt'] ?? null) ? (float) $payload['pushedAt'] : microtime(true);
    $durationMs = max(0.0, (microtime(true) - $startTime) * 1000);
    // ...
}
```

The package already records when processing starts. `JobProcessingListener::handle()` runs on `JobProcessing` and takes the CPU and memory baselines (`JobCpuSnapshotCache::store()`, `JobMemorySnapshotCache::store()`, each stamped with `stored_at = microtime(true)`). It also calls `RecordJobStartAction` with `startedAt: Carbon::now()`. The processing start time is available in-process. `collect()` just doesn't use it.

The package's own test fixtures show what was intended. They build payloads with `'pushedAt' => microtime(true) - 0.15` and call it a "simulated duration". So `pushedAt` was meant to stand for the processing start, which is not what it means in Horizon payloads.

### Reproduction

1. Install `cboxdk/laravel-queue-metrics` with persistence enabled and no Horizon.
2. Dispatch a job whose `handle()` sleeps for 1 s and process it with `queue:work`. The recorded duration is roughly `0 ms`, not `1000 ms`.
3. Register `Queue::createPayloadUsing(fn () => ['pushedAt' => microtime(true)])`, dispatch the same job, wait 30 s, then start `queue:work`. The recorded duration is roughly `31000 ms`, not `1000 ms`.

### Proposed fix (backwards compatible)

Record a processing start time on `JobProcessing` and measure the duration from it. Use `pushedAt` only as a last-resort fallback, or drop that fallback.

1. Add a small in-process cache next to the CPU and memory ones, for example `Support/JobStartTimeCache` with `store(string $jobId, float $startedAt)`, `get()` and `forget()`, and stale-entry eviction like `JobCpuSnapshotCache`.
2. In `JobProcessingListener::handle()`, store `microtime(true)` for the job **unconditionally**. Do it before the `getmypid()` / `ProcessMetrics` branch, which can be skipped.
3. In `JobMetricsCollector::collect()`:

```php
$startTime = JobStartTimeCache::get($jobId)
    ?? microtime(true); // job never seen starting in this process: report 0 rather than a dispatch-relative value

$durationMs = max(0.0, (microtime(true) - $startTime) * 1000);
```

and add `JobStartTimeCache::forget($jobId)` to the existing `finally` block.

Notes for the implementation:

- Stale-entry eviction must **not** be 600 s like `JobCpuSnapshotCache::MAX_AGE_SECONDS`. Jobs regularly run longer than 10 minutes (our job timeouts go up to 1140 s). If the start entry were evicted, a long job would report `0 ms`. Base the age on the longest expected job, for example `max(3600, queue.connections.*.retry_after)`, or evict only entries for jobs that have finished.
- A job that times out (the worker process is killed) never reaches `collect()`. Eviction deals with that, as it already does for the CPU and memory caches.
- If keeping the old `pushedAt` behaviour for Horizon users matters, put it behind a config flag such as `queue-metrics.duration_source` (`processing` | `pushed_at`) with `processing` as the default. Since this is a bug fix, plain `processing` is reasonable.

### Suggested tests

- A job with no `pushedAt` that runs for about 150 ms records a duration of about 150 ms. Today it records about 0.
- A job with `pushedAt` 30 s in the past that runs for about 150 ms records about 150 ms. Today it records about 30,150.
- A failed job (`JobFailedListener`) records its run time, not its wait time.
- The start-time entry is removed after `collect()` on both success and failure.
- A job that starts and finishes after the CPU snapshot cache's 600 s window still reports its real duration (make sure the new cache's eviction does not drop it).

### Suggested PR title & description

**Title:** Measure job duration from when processing started, not from `pushedAt`

**Description:**

> `JobMetricsCollector::collect()` computes duration as `now - $payload['pushedAt']`. `pushedAt` is the dispatch time (Horizon sets it at push, and apps that want `queue-autoscale`'s pickup-time signal stamp it too), so the recorded duration is queue wait plus run time. Without `pushedAt` it falls back to `now`, so every duration is about 0 ms. In both cases, average duration, baselines, anomaly detection and the autoscaler's `avgJobTime` get the wrong value. With `pushedAt` the autoscaler overshoots under backlog, because wait is counted twice. Without it, real durations never reach the autoscaler.
>
> This records `microtime(true)` on `JobProcessing` in an in-process cache, like the CPU and memory baselines that are already taken there, and measures from it in `collect()`. The entry is removed in the existing `finally` block. Eviction allows long-running jobs. Adds tests for jobs with and without `pushedAt`, failed jobs, and long-running jobs.

---

## Part 2 — `cboxdk/laravel-queue-autoscale`: pickup time counts delays and earlier attempts as waiting

### Target package

- **Package:** `cboxdk/laravel-queue-autoscale`
- **Version observed:** `4.3.1`
- **Repository:** https://github.com/cboxdk/laravel-queue-autoscale
- **Affected class:** `Cbox\LaravelQueueAutoscale\Pickup\PickupTimeRecorder::handle()`

### Summary

`PickupTimeRecorder` records `microtime(true) - $payload['pushedAt']` on every `JobProcessing` event. That is "time since dispatch", and it is only equal to "time spent waiting for a worker" for a job's first attempt with no delay. Three cases inflate the p95 that `HybridStrategy` scales on:

1. **Delayed jobs.** A job dispatched with `->delay(600)` reports a pickup of 600 s or more even when a worker took it the moment it became available. Laravel puts the delay in the payload (`$payload['delay']`, in seconds, set by `Queue::createPayload()`), so the recorder can subtract it.
2. **Re-attempts.** A job released back to the queue (`$this->release($backoff)`, or after an exception with backoff) keeps its original `pushedAt`. Its second sample includes the first run and the backoff.
3. **Failed jobs pushed again with `queue:retry`.** The payload keeps the original `pushedAt` and the new SQS message starts again at attempt 1. The sample is the time since the original dispatch, which can be hours or days.

Any of these push p95 pickup toward or past the SLA. The autoscaler then reports SLA breaches and scales up for work that is not waiting at all. Delayed jobs are common: notifications, scheduled follow-ups, rate-limited retries.

There is also a related gap. **The package never stamps `pushedAt`.** Outside Horizon, the pickup signal does not exist unless the app adds its own payload hook, which is what Advising App does.

### Proposed fix

In `PickupTimeRecorder::handle()`:

```php
if ($event->job->attempts() > 1) {
    return; // released or retried: pushedAt no longer marks when this attempt became available
}

$availableAt = (float) $pushedAt + (int) ($payload['delay'] ?? 0);

$pickupSeconds = max(0.0, microtime(true) - $availableAt);
```

For `queue:retry`, the attempt count is reset, so the check above does not catch it. Either:

- drop samples above a sane maximum (for example a `pickup.max_sample_seconds` config defaulting to a few times the largest SLA), or
- document that apps re-pushing failed jobs should refresh `pushedAt`.

The first is simpler and also protects against clock skew.

**Optional, opt-in:** stamp `pushedAt` from the package.

- Register `Queue::createPayloadUsing(fn ($connection, $queue, $payload) => isset($payload['pushedAt']) ? [] : ['pushedAt' => microtime(true)])` behind a config flag such as `pickup.stamp_pushed_at`. It keeps Horizon's value when present.
- This makes the pickup signal work for SQS, database and Redis queues without app code.
- It must ship only after (or with) Part 1. Otherwise every app that turns it on gets wait-inflated durations from queue-metrics.

### Suggested tests

- A job with `delay = 600` picked up immediately when available records about 0 s, not about 600 s.
- A job on attempt 2 records no sample.
- A sample above `pickup.max_sample_seconds` is dropped.
- With `pickup.stamp_pushed_at` on, a payload without `pushedAt` gets one, and a payload that already has one keeps it.

### Suggested PR title & description

**Title:** Exclude dispatch delays and re-attempts from pickup time (and optionally stamp `pushedAt`)

**Description:**

> `PickupTimeRecorder` measures pickup as `now - pushedAt` on every attempt. For delayed jobs that includes the delay, because Laravel already puts it in the payload as `delay`. For released or retried jobs it includes earlier runs and backoff. For `queue:retry` it can be hours or days. These samples inflate the p95 pickup that `HybridStrategy` scales on, which causes false SLA breaches and over-scaling.
>
> This skips samples from attempts after the first, measures from `pushedAt + delay`, and drops samples above `pickup.max_sample_seconds`. It also adds an opt-in `pickup.stamp_pushed_at`, so non-Horizon apps get a pickup signal without their own payload hook. That option should only be enabled together with the queue-metrics duration fix, which measures job duration from processing start. Tests cover delayed, released, retried and stamped payloads.

---

## Consuming-app cleanup (after these ship)

> **Implementing the upstream fixes? Ignore this section.** It lists Advising App changes to make **after** the fixes are released and we upgrade. They are not part of the package changes.

Both packages are pinned to **exact** versions in `composer.json`. Bump the pin, then run `pls exec app composer update <package>`.

### After Part 1 (queue-metrics) is released

- Bump the exact `cboxdk/laravel-queue-metrics` pin.
- Remove the second comment line above `Queue::createPayloadUsing(...)` in `app/Providers/QueueServiceProvider.php`. It points to this document.
- Revisit `queue-autoscale.scaling.fallback_job_time_seconds`: real durations will now reach the autoscaler instead of the fallback.

### After Part 2 (queue-autoscale) is released

- Bump the exact `cboxdk/laravel-queue-autoscale` pin.
- **Delay handling:**
    - Delete `App\Queue\TenantFairSqsQueue::createPayload()`.
    - Delete its test, "moves `pushedAt` to when a delayed job becomes available", in `tests/Landlord/Queue/TenantFairSqsQueueTest.php`.
    - **Do this in the same change as the upgrade.** Otherwise the delay is subtracted twice, and delayed jobs report a pickup of 0.
- **Re-attempts and `queue:retry`:** nothing to remove. If the sample cap shipped, our failed-job retry job no longer needs to refresh `pushedAt` when it re-pushes a job. Leaving the refresh in is harmless.
- **If `pickup.stamp_pushed_at` shipped:**
    - Turn it on in `config/queue-autoscale.php`.
    - Delete the `Queue::createPayloadUsing(...)` hook and its comment from `app/Providers/QueueServiceProvider.php`.
    - Change `tests/Landlord/Providers/QueueServiceProviderTest.php`'s "stamps `pushedAt` on job payloads" to cover the config instead, or delete it.

### When both are done

- Delete this document.

# Upstream fixes — `cboxdk` queue packages: how they identify a queue (SQS URLs, colons in queue names, the `sync` queue)

This document tracks three related contributions. All three are about the queue name the packages key their data by.

- **Part 1 — `cboxdk/laravel-queue-metrics`:** stored keys are split on every `:`, so a queue name containing a colon is cut short. PR Opened: _not yet_
- **Part 2 — `cboxdk/laravel-queue-metrics` and `cboxdk/laravel-queue-autoscale`:** processing-side data is keyed by `$job->getQueue()`, which is the full queue URL on SQS, while everything else uses the queue name. PR Opened: _not yet_
- **Part 3 — `cboxdk/laravel-queue-autoscale`:** jobs run synchronously are discovered as a `sync` workload the autoscaler can never run. PR Opened: _not yet_

The three parts are independent and can land in any order. Part 2 is the one Advising App works around in code; see [Consuming-app cleanup](#consuming-app-cleanup-after-these-ship).

## Environment where reproduced

- Laravel `13.30.1`, PHP `8.4`.
- Queue: Amazon SQS standard queues (dev and production), Redis Cluster (local development). No Horizon.
- Packages: `cboxdk/laravel-queue-autoscale` `4.3.1`, `cboxdk/laravel-queue-metrics` `3.4.0`, `cboxdk/laravel-queue-monitor` `1.11.0`.

## How it showed up

On the dev environment (SQS), during a load test with 89 workers busy on the default queue, the queue monitoring page showed:

- **Throughput/min `0.0`** and a blank **p95 Pickup** for every queue;
- two queues that do not exist: **`https`** and **`sync`**, each with 0 workers.

---

## Part 1 — `cboxdk/laravel-queue-metrics`: keys are split on every `:`

### Target package

- **Package:** `cboxdk/laravel-queue-metrics`
- **Version observed:** `3.4.0`
- **Repository:** https://github.com/cboxdk/laravel-queue-metrics
- **Affected code:**
    - `Services\QueueMetricsQueryService::getAllQueuesWithMetrics()` — the key parser, `explode(':', $keyWithoutPrefix)`;
    - `Services\JobMetricsQueryService` — the same parser;
    - `Actions\CalculateBaselinesAction::getJobClassesForQueue()` — `explode(':', $key)`, then `array_slice($parts, 4)`.
- **Downstream consumer affected:** `cboxdk/laravel-queue-autoscale` discovers workloads from `getAllQueuesWithMetrics()` (`Scaling\WorkloadDiscovery::discover()`).

### Summary

`Support\MetricsKeyBuilder::key()` joins segments with `:`, giving keys such as `jobs:{connection}:{queue}:{jobClass}`. The parsers above split those keys on **every** `:` and take fixed positions, so any queue name that itself contains a colon is cut at its first colon.

Some of the repositories already split with a limit (`explode(':', $member, 3)`), and job classes are rebuilt with `implode(':', array_slice(...))`, so colons were anticipated there. The queue segment was missed.

### Reproduction

1. Dispatch any job onto a queue whose name contains a colon, for example a Redis queue named `reports:nightly`, and let a worker process it.
2. Call `QueueMetrics::getAllQueuesWithMetrics()`.
3. The queue comes back as `reports`, not `reports:nightly`. The autoscaler then discovers a `reports` workload.

On SQS the same happens to every queue, because of Part 2: the processed-job keys contain the queue URL, so every queue is discovered as `https`.

### Suggested fix

Encode the queue segment when building a key (for example `rawurlencode()`) and decode it when parsing, so the queue can never contain the separator. Splitting on the last `:` instead does not work, because job display names can contain colons too (a queued closure's is `Closure (routes/console.php:12)`).

### Tests

- A key with a colon in the queue name is parsed back to the full queue name, in both query services and in `CalculateBaselinesAction`.
- `getAllQueuesWithMetrics()` returns one entry for `reports:nightly` and none for `reports`.

---

## Part 2 — `cboxdk/laravel-queue-metrics` and `cboxdk/laravel-queue-autoscale`: processing data is keyed by the SQS queue URL

### Target packages

- **Packages:** `cboxdk/laravel-queue-metrics` (`3.4.0`), `cboxdk/laravel-queue-autoscale` (`4.3.1`)
- **Repositories:** https://github.com/cboxdk/laravel-queue-metrics, https://github.com/cboxdk/laravel-queue-autoscale
- **Writers keyed by `$job->getQueue()`:**
    - queue-metrics `Listeners\JobProcessingListener`, `JobProcessedListener`, `JobFailedListener`, `JobExceptionOccurredListener`, `JobTimedOutListener`, `JobDebouncedListener`;
    - queue-autoscale `Pickup\PickupTimeRecorder::handle()` and `Fuse\JobOutcomeRecorder`.
- **Readers keyed by the queue name:**
    - queue-metrics `Listeners\JobQueuedListener` (records the queued side by `$event->queue`), `QueueMetrics::getQueueMetrics()` callers;
    - queue-autoscale `Scaling\Strategies\HybridStrategy` (reads pickup samples for `$config->sampleQueues()`), `Fuse\FailureFuse` (evaluates each workload by its queue name).

### Summary

Laravel's `SqsQueue::pop()` builds each `SqsJob` with the full queue URL, so on SQS `$job->getQueue()` returns `https://sqs.us-west-2.amazonaws.com/<account>/<queue>`. Redis and database jobs return the queue name. Everywhere else the queue is identified by name: `onQueue()`, the `JobQueued` event, `queue:work --queue`, and the packages' own configuration.

So on SQS, the packages write processing-side data under the URL and read it back under the name, and nothing matches:

| Data                              | Written under | Read under | Effect on SQS                                                                           |
| --------------------------------- | ------------- | ---------- | --------------------------------------------------------------------------------------- |
| Queued count / queued-at          | name          | name       | Works.                                                                                  |
| Processed, failed, duration, etc. | URL           | name       | Throughput and processing time are empty; one job is split across two queue identities. |
| Pickup-time samples               | URL           | name       | `HybridStrategy` never gets the p95 pickup signal, so it scales on backlog alone.       |
| Failure fuse outcomes             | URL           | name       | The fuse can never trip.                                                                |

The URL also contains `:`, which is how Part 1 turns every SQS queue into `https`.

### Reproduction

1. Configure an SQS connection and dispatch a few jobs to a queue, for example `AdvisingApp`, and let a worker process them.
2. `QueueMetrics::getQueueMetrics('sqs', 'AdvisingApp')->throughputPerMinute` is `0.0`.
3. `app(PickupTimeStoreContract::class)->recentSamples('sqs', 'AdvisingApp', 300)` is empty, while the same call with the queue URL returns samples.

### Suggested fix

Normalise the queue before keying, in one shared helper used by every listener above. When the value is a URL, use the last path segment, then remove the connection's configured `suffix` (`queue.connections.{connection}.suffix`) if present, which inverts `SqsQueue::getQueue()`. Anything that is not a URL is used as-is.

Sentry's Laravel integration already does this for the same reason: `Sentry\Laravel\Features\QueueIntegration::normalizeQueueName()` reduces an SQS URL to the queue name.

### Tests

- With an `SqsJob` whose queue is a URL, every listener above records under the queue name.
- With a suffix configured, the suffix is removed.
- Non-URL queue names (Redis, database) are unchanged.

---

## Part 3 — `cboxdk/laravel-queue-autoscale`: jobs run synchronously become a `sync` workload

### Target package

- **Package:** `cboxdk/laravel-queue-autoscale`
- **Version observed:** `4.3.1`
- **Repository:** https://github.com/cboxdk/laravel-queue-autoscale
- **Affected code:** `Scaling\WorkloadDiscovery::discover()`, which takes every queue `QueueMetrics::getAllQueuesWithMetrics()` returns, on any connection.

### Summary

Laravel's `SyncJob::getQueue()` always returns `'sync'`. Every job run synchronously (`dispatch_sync()`, the `sync` connection, or a deferred/background connection that runs jobs through `sync`) fires the queue events, so queue-metrics records it under connection `sync`, queue `sync`. The autoscaler then discovers a `sync` workload. Nothing can ever pop from the `sync` connection, so it is noise in every scaling cycle, cluster summary and dashboard.

### Reproduction

1. Run any job with `dispatch_sync()`.
2. On the next manager cycle, the discovered workloads include connection `sync`, queue `sync`.

### Suggested fix

Preferably, skip discovered workloads on connections that cannot be worked, by the connection's **driver** rather than the queue's name: at least `sync`, and possibly `null` and `deferred`/`background`. Filtering by driver cannot hide a real queue.

A lighter alternative, which we only lightly recommend, is to ship `'sync'` in the default `excluded` list. It is simpler, but `excluded` matches queue **names** on every connection, so it would also hide a real queue that happens to be named `sync`. If that route is taken, the config comment should say so.

### Tests

- A workload on a `sync`-driver connection is not discovered.
- A real queue named `sync` on a non-sync connection is still discovered.

---

## Consuming-app cleanup (after these ship)

> **Implementing the upstream fixes? Ignore this section.** It lists Advising App changes to make **after** the fixes are released and we upgrade. They are not part of the package changes.

The `cboxdk` packages are pinned to exact versions in `composer.json`. To pick up a release, bump the pin, then run `pls exec app composer update <package>`.

### After Part 1 ships

No Advising App change. We have no workaround for it: the Part 2 workaround already keeps SQS URLs out of these keys.

### After Part 2 ships (in both queue-metrics and queue-autoscale)

Revert the `getQueue()` override, so we no longer carry it:

1. In `App\Queue\Jobs\TenantFairSqsJob`, remove the constructor and the `getQueue()` override, and the `SqsClient` and `Container` imports they added.
2. In `App\Queue\TenantFairSqsQueue::pop()`, pass the queue URL as the job's queue again and drop the extra queue-name argument, the `$queueName` variable and the `enum_value` import, matching upstream `SqsQueue::pop()`.
3. Update the tests:
    - `tests/Landlord/Queue/Jobs/TenantFairSqsJobTest.php`: remove the queue-name argument from `tenantFairSqsJobWithBody()`.
    - `tests/Landlord/Queue/TenantFairSqsQueueTest.php`: remove "names a popped job by its queue but deletes and releases it through the queue URL", the `TenantFairSqsQueueTestQueue` enum it uses, and the `changeMessageVisibility` recording added to `tenantFairSqsQueueRecordingInto()` for it.
4. In `docs/explanations/oss-todo/laravel-sqs-missing-overflow-payload.md`, remove "and still passing it the queue name (`enum_value($queue) ?: $this->default`) as well as the URL" from the note about re-copying `pop()`.
5. Check on dev (SQS) that the queue monitoring page shows throughput and p95 pickup for each queue, and that no `https` queue appears.

After the revert, `failed_jobs.queue` for new failures, Sentry's `queue` tag and worker log lines go back to showing the queue URL. That is expected and harmless: `queue:retry` resolves a URL as well as a name.

### After Part 3 ships

- **If upstream filters by connection driver:** remove `'sync'` and its comment from `excluded` in `config/queue-autoscale.php`.
- **If upstream adds `'sync'` to the default `excluded` list instead:** keep our entry. `config/queue-autoscale.php` is published, so the package's default list does not apply to us.

### Then

Delete this document.

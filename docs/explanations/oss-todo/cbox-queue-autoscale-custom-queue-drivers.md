# Upstream fix: `cboxdk/laravel-queue-autoscale` recognises a queue connection only by its driver name

PR Opened: https://github.com/cboxdk/laravel-queue-autoscale/pull/82

## Target package

- **Package:** `cboxdk/laravel-queue-autoscale`
- **Version observed:** `4.6.0`
- **Repository:** https://github.com/cboxdk/laravel-queue-autoscale
- **Affected code:**
    - `Configuration\AutoscaleConfiguration::connectionCanBeWorked()`
    - `Scaling\SlaSignal::DRIVERS_REPORTING_OLDEST_JOB_AGE` / `driverReportsOldestJobAge()`
    - `Diagnostics\ConfigurationDoctor`: the SQS checks (`connectionDriver(...) === 'sqs'`, `discoveredOnDriver('sqs')`, `hasSqsConnection()`)

## Environment

- Laravel `13.35`, PHP `8.4`.
- `cboxdk/laravel-queue-autoscale` `4.6.0`, `cboxdk/laravel-queue-metrics` `3.5.0`.
- Two queue connections use custom drivers registered with `Queue::addConnector()`:
    - `sqs` uses the `tenant-fair-sqs` driver, whose queue class extends `Illuminate\Queue\SqsQueue`.
    - `background` uses the `tenant-aware-background` driver, whose queue class extends `Illuminate\Queue\BackgroundQueue`.

## The problem

Laravel lets an app register its own queue driver, usually a subclass of a built-in queue. The package decides what a connection is by comparing `queue.connections.<name>.driver` with Laravel's built-in driver names, so a custom driver is never recognised:

- **Unworkable connections.** `connectionCanBeWorked()` treats `sync`, `null`, `deferred` and `background` as connections `queue:work` cannot pop. A connection whose driver is a custom subclass of `BackgroundQueue` (or `SyncQueue`, `DeferredQueue`, `NullQueue`) is judged workable. Its jobs are reported under the `sync` queue, so discovery presents a `sync` workload that no worker can ever run.
- **Doctor.** `queue:autoscale:doctor` only runs its SQS checks (pickup-time samples on SQS) for connections whose driver is exactly `sqs`. A custom subclass of `SqsQueue` is skipped.
- **SLA signal.** `driverReportsOldestJobAge()` only trusts `redis` and `database`. A custom subclass of `RedisQueue` or `DatabaseQueue` is treated as unable to report the oldest job's age. This one is harmless for SQS subclasses (they cannot report it either), but wrong for Redis and database ones.

## Proposed change

Recognise a connection by the class of the queue it resolves to, and fall back to the driver name:

1. Add one helper (for example in `AutoscaleConfiguration`) that resolves a connection through `Illuminate\Queue\QueueManager::connection()` and caches the queue class per connection for the process. If resolving throws (missing credentials, an unconfigured connection), it returns `null` and callers use the driver name as today.
2. `connectionCanBeWorked()`: not workable when the resolved queue is an instance of `SyncQueue`, `NullQueue`, `DeferredQueue` or `BackgroundQueue` (the last two behind `class_exists`, since older Laravel 12 releases lack them); otherwise fall back to the existing driver list.
3. Doctor: treat a connection as SQS when its queue is an instance of `SqsQueue`, or its driver is `sqs`.
4. SLA signal: a connection reports the oldest job's age when its queue is an instance of `RedisQueue` or `DatabaseQueue`, or its driver is in the existing list.

No config or API changes. Apps using only built-in drivers resolve to the same answer they get today.

### Out of scope

- `cboxdk/laravel-queue-monitor` also checks `driver === 'sqs'`, but only to detect FIFO queues. Advising App does not use FIFO queues.

## Suggested tests

- A connection with a custom driver whose queue extends `BackgroundQueue` (and `SyncQueue`) cannot be worked.
- The doctor runs its SQS checks for a custom driver whose queue extends `SqsQueue`.
- A custom driver whose queue extends `RedisQueue` reports the oldest job's age.
- A connection that fails to resolve falls back to its driver name.
- Built-in drivers keep their current answers.

## Downstream workaround (current Advising App code)

`config/queue-autoscale.php` lists `'sync'` under `excluded`, so the `sync` workload our `background` connection produces is never managed. Nothing works around the doctor or SLA cases: the doctor simply skips our SQS connection.

## Consuming-app cleanup (after this ships)

> **Implementing the upstream fix? Ignore this section.** It lists Advising App changes to make **after** the fix is released and we upgrade. They are not part of the package change.

1. Bump the exact pin of `cboxdk/laravel-queue-autoscale` in `composer.json`, then run `pls exec app composer update cboxdk/laravel-queue-autoscale`.
2. In `config/queue-autoscale.php`, remove `'sync'` and its comment from `excluded`.
3. Check that `pls exec app php artisan queue:autoscale:doctor` now runs its SQS checks for the `sqs` connection.

### Then

Delete this document.

## Suggested PR title & description

**Title:** Recognise custom queue drivers by the queue class they resolve to

> Laravel apps can register their own queue driver, usually a subclass of a built-in queue. The package compares `queue.connections.*.driver` with Laravel's built-in names, so a custom driver is never recognised: a subclass of `BackgroundQueue` is treated as workable and shows up as a `sync` workload no worker can run, the doctor skips SQS checks for a subclass of `SqsQueue`, and a subclass of `RedisQueue` is treated as unable to report the oldest job's age.
>
> This resolves each connection once and checks the queue class with `instanceof`, falling back to the driver name when the connection cannot be resolved. Built-in drivers get the same answers as before.

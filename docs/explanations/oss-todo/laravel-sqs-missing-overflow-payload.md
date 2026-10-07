# Upstream fix — `laravel/framework`: an SQS message whose overflow payload is missing breaks the worker instead of being discarded

PR Opened: _not yet_

## Target package

- **Package:** `laravel/framework`
- **Version observed:** `13.30.1`
- **Repository:** https://github.com/laravel/framework
- **Affected classes:** `Illuminate\Queue\Jobs\SqsJob::getRawBody()`, `Illuminate\Queue\SqsQueue::pop()`
- **Feature:** SQS overflow storage (large payloads offloaded to a cache store and replaced with a `{"@pointer": "..."}` message), added in [#59734](https://github.com/laravel/framework/pull/59734).
- **Target branch:** `13.x` (bug fix).

## Environment where reproduced

- Laravel `13.30.1`, PHP `8.4`.
- Queue: Amazon SQS standard queues, `overflow.enabled = true`, `delete_after_processing = true`.
- Worker: `php artisan queue:work` (default `--tries=1`; also checked with `--tries=0`).

## Why Advising App cares

Advising App sends job payloads of 1 MiB or more through overflow storage. They are kept on S3, under the S3 root of the tenant that dispatched them. Our old SQS driver (`defectivecode/laravel-sqs-extended`) hit the same failure in production, which is why the app had a `SqsDiskJob::getRawBody()` override that deleted such messages. When we moved to Laravel's own overflow support, that override had to come along as a guard in our own job class. This contribution would let Laravel handle it.

## Summary

When a message is an overflow pointer and the stored payload no longer exists, `SqsJob::getRawBody()` returns `null`. Its docblock says it returns `string`. Nothing checks for this. The worker goes on processing a job with no payload:

- `Job::payload()` becomes `json_decode(null, true)`. That is a deprecation on PHP 8.1+ and returns `null`.
- Every payload-derived setting (`maxTries`, `backoff`, `retryUntil`, `timeout`, `failOnTimeout`, `maxExceptions`) silently reads as `null`. So the job's own retry settings are ignored, and only the worker's `--tries` applies.
- `Job::fire()` throws `ErrorException: Trying to access array offset on value of type null` at `$payload['job']`. Nothing in the error says the overflow payload is missing.

What happens next depends on the worker's `--tries`:

| Worker `--tries`      | Outcome                                                                                                                                                                                                                                                                                                                                                                                                                            |
| --------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `0` (unlimited)       | `handleJobException()` releases the job and SQS redelivers it every visibility timeout until the queue's retention period expires (up to 14 days), throwing the same misleading `ErrorException` each time. With a redrive policy, the useless pointer ends up in the DLQ.                                                                                                                                                         |
| `1` (default) or more | On the last attempt, `Job::fail()` deletes the message, then `failed()` throws again on `$payload['job']`. Then `WorkCommand::logFailedJob()` passes the `null` body to the failed-job provider, and `DatabaseUuidFailedJobProvider::log()` fails on `json_decode($payload, true)['uuid']`. So **no `failed_jobs` record is written**. The job disappears, and all that is left is a few unrelated-looking exceptions in the logs. |

## How a payload goes missing

- **Redelivery race:** a job runs past the visibility timeout. SQS redelivers it while the first attempt is still running. The first attempt finishes and `delete()` removes the overflow payload (`delete_after_processing`), so the second delivery points at nothing.
- **Storage expiry:** the overflow store expires or evicts the entry, for example a cache TTL, an S3 lifecycle rule or Redis eviction, while the message is still queued or delayed.
- **Manual cleanup**, or a payload written to a different store or prefix than the one the worker reads, for example after a config change during a deploy.

## Reproduction

1. Enable overflow on an SQS connection with a cache store, for example `array` or `redis`.
2. Dispatch a job whose payload is 1 MiB or more, so it is sent as `{"@pointer": "laravel:sqs-payloads:<uuid>"}`.
3. Remove the entry: `Cache::store($store)->forget('laravel:sqs-payloads:<uuid>')`.
4. Run `queue:work --tries=0`. The `ErrorException` repeats on every redelivery.
5. Run `queue:work --tries=1`. The message is deleted, no `failed_jobs` row is written, and there are three exceptions in the log.

The same can be shown in a unit test with a mocked `SqsClient` and an `ArrayStore`. Construct an `SqsJob` whose body is a pointer to a missing key and assert what `getRawBody()` / `payload()` return.

## Proposed fix

Discard the broken message before it reaches the worker's processing path, and say why.

**Preferred, in `SqsQueue::pop()`:** after building the job, check that the overflow payload resolves. If it does not, delete the message and throw a dedicated exception:

```php
$job = new SqsJob(
    $this->container, $this->sqs, $response['Messages'][0],
    $this->connectionName, $queue, $this->overflowStorage
);

if ($job->isMissingOverflowPayload()) {
    $job->delete();

    throw new MissingOverflowPayloadException($job->getJobId(), $job->overflowPointer());
}

return $job;
```

- `SqsJob::isMissingOverflowPayload()` returns `true` when the body is an overflow pointer and `overflowStore()->get($pointer)` is `null`. The fetched payload should be memoized in `$cachedRawBody` so it isn't fetched twice; it is read moments later anyway.
- `Worker::getNextJob()` already catches and reports any exception thrown by `pop()`, then moves on. The report says exactly what happened, and no listener or failed-job provider ever sees a `null` payload.
- Deleting is correct: there is nothing left to retry, and sending a pointer to the DLQ helps no one.

**Alternative, in `SqsJob::getRawBody()`:** throw `MissingOverflowPayloadException` there instead of returning `null`. It is smaller, but the worker then fails inside `process()`. That re-reads the payload in `handleJobException()` and either releases the message or fails it without a payload, so the message still has to be deleted somewhere. The `pop()` version keeps the behaviour in one place.

Either way, `getRawBody()`'s docblock should stay `string` and be true.

## Suggested tests

- A pointer to a missing payload: `pop()` deletes the message (`deleteMessage` called once) and throws `MissingOverflowPayloadException`, whose message includes the message id and the pointer.
- A pointer to an existing payload: `pop()` returns the job, and `getRawBody()` returns the payload without a second store read.
- A non-pointer body: unchanged.
- Overflow disabled: a body that merely looks like a pointer is not treated as one, which is unchanged.

## Downstream workaround (current Advising App code)

`App\Queue\Jobs\TenantFairSqsJob::getRawBody()`:

```php
$rawBody = $legacyPointer
    ? $this->overflowStorage()->disk($this->tenantId())?->get($legacyPointer)
    : parent::getRawBody();

if (! is_string($rawBody)) {
    if (! $this->isDeleted()) {
        $this->delete();
    }

    throw new RuntimeException("The offloaded payload of SQS message [{$this->getJobId()}] could not be found.");
}

return $this->cachedRawBody = $rawBody;
```

- **What it covers:**
    - Laravel overflow pointers whose payload is gone;
    - pointers naming a tenant that no longer exists (`SqsOverflowStorage::store()` returns a `NullStore`);
    - the temporary old-format pointers from `defectivecode/laravel-sqs-extended`.
- **Where it runs:** `TenantFairSqsQueue::pop()` is a copy of `SqsQueue::pop()` that returns `TenantFairSqsJob`, which is how the guard is reached.
- **Tests:** `tests/Landlord/Queue/Jobs/TenantFairSqsJobTest.php`:
    - "deletes the message and throws when its offloaded payload is missing";
    - "deletes the message and throws when the tenant named in the message no longer exists";
    - the legacy "deletes the message and throws when its tenant no longer exists".

## Consuming-app cleanup (after this ships)

> **Implementing the upstream fix? Ignore this section.** It lists Advising App changes to make **after** the fix is released and we upgrade. They are not part of the framework change.

1. Run `pls exec app composer update laravel/framework` to get a release that contains the fix. The constraint is `^13.0`, so no `composer.json` change is needed unless the fix only ships in a new major.
2. **If the fix landed in `SqsQueue::pop()` (the preferred design):** `App\Queue\TenantFairSqsQueue::pop()` is a copy of the old `pop()` and does **not** inherit it.
    - Re-copy the new upstream `pop()` body into our override, keeping `new TenantFairSqsJob(...)` in place of `new SqsJob(...)` and still passing it the queue name (`$queue ?? $this->default`) as well as the URL.
    - Then remove the guard.
    - Re-check this copy on every Laravel upgrade.
3. **If the fix landed in `SqsJob::getRawBody()`:** our override already calls `parent::getRawBody()`, so the upstream exception passes through. Remove the guard.
4. **Removing the guard:**
    - **If the `sqs-native-overflow` cleanup task has already been done** (the legacy pointer handling is gone): delete the whole `getRawBody()` override and the `RuntimeException` import.
    - **If the legacy pointer handling is still there:** upstream does not know that format, so keep the guard for the legacy branch only. Alternatively, do that cleanup task first.
5. **Update the tests in `tests/Landlord/Queue/Jobs/TenantFairSqsJobTest.php`.** Keep the missing-payload and missing-tenant tests, because they prove the upstream behaviour still works with our per-tenant `overflowStore()`. Change them to:
    - expect the upstream exception class;
    - for the `pop()` design, go through `TenantFairSqsQueue::pop()` rather than calling `getRawBody()` directly.
6. Delete this document.

## Suggested PR title & description

**Title:** [13.x] Discard SQS messages whose overflow payload is missing

**Description:**

> When an SQS message is an overflow pointer and its stored payload is gone (a redelivery after `delete_after_processing` removed it, an expired or evicted store entry, a manual cleanup), `SqsJob::getRawBody()` returns `null` despite its `string` return type. The worker then processes a job with no payload:
>
> - every payload-derived setting reads as `null`;
> - `fire()` throws an unrelated-looking `Trying to access array offset on value of type null`.
>
> With `--tries=0` the message is redelivered until the queue's retention period expires. With `--tries>=1` the message is deleted, but `failed()` and the failed-job provider both throw on the `null` payload, so no `failed_jobs` record is written.
>
> This checks for the payload in `SqsQueue::pop()`. If it is missing, the message is deleted and a `MissingOverflowPayloadException` naming the message and pointer is thrown, which the worker already reports and moves past. The resolved payload is memoized, so a healthy pointer costs no extra read. Tests cover missing, present and non-pointer bodies.

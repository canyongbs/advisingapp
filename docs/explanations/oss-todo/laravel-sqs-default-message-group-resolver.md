# Upstream feature — `laravel/framework`: a default SQS message group resolver (for SQS fair queues)

PR Opened: _not yet_

## Target package

- **Package:** `laravel/framework`
- **Version observed:** `13.30.1`
- **Repository:** https://github.com/laravel/framework
- **Affected class:** `Illuminate\Queue\SqsQueue::getQueueableOptions()`
- **Target branch:** `13.x` if accepted as a small additive feature, otherwise `master`.

## Environment

- Laravel `13.30.1`, PHP `8.4`.
- Queue: Amazon SQS **standard** queues with SQS fair queuing (`MessageGroupId` on standard queues).
- Multi-tenant app (`spatie/laravel-multitenancy`). Jobs are dispatched from hundreds of call sites while a tenant is current.

## Why Advising App wants it

In 2025 AWS added fair queues to SQS standard queues. Messages carry a `MessageGroupId`, usually the tenant id. When one group has a disproportionate share of in-flight messages, SQS delivers the other groups' messages first. A quiet tenant's job no longer waits behind a noisy tenant's backlog. Our decision record is `docs/explanations/decisions/0002-static-sqs-fair-queues-for-tenant-isolation.md`.

Laravel already sends `MessageGroupId` on standard queues, but only when the **job** defines a group (`$job->messageGroup`, `messageGroup()`, or `onGroup()`). To group every job by tenant, an app would have to touch every job class or every dispatch. The practical choice today is to subclass `SqsQueue` and override `getQueueableOptions()`, which is what we do. That override is copied logic tied to an internal method, and it would be one line of configuration if Laravel offered a default.

## Proposed API

A static resolver, in the same style as `Queue::createPayloadUsing()`:

```php
use Illuminate\Queue\SqsQueue;

SqsQueue::resolveMessageGroupUsing(fn (mixed $job, ?string $queue): ?string => Tenant::current()?->getKey());

SqsQueue::resolveMessageGroupUsing(null); // reset
```

### Precedence: the job always wins

The resolver is only a **default**. Whatever the job sets is used first, exactly as today. The resolver is consulted only when the job does not set a group. For FIFO queues, the queue name stays the last fallback, so FIFO behaviour is unchanged when the resolver returns `null`:

1. `$job->messageGroup`, set by `onGroup()` or a `$messageGroup` property.
2. `$job->messageGroup()` method.
3. **The resolver**, new. Returning `null` means "no group".
4. FIFO only: the queue name, which is today's FIFO fallback.

So a job can always opt out of the tenant default, for example a tenant job that must share a group with a landlord workflow, just by setting its own group.

### Change inside `getQueueableOptions()`

```php
$messageGroupId = null;

if ($isObject) {
    $messageGroupId = transform($job->messageGroup ?? (method_exists($job, 'messageGroup') ? $job->messageGroup() : null), $transformToString);
}

if ($messageGroupId === null && static::$messageGroupResolver) {
    $messageGroupId = transform(call_user_func(static::$messageGroupResolver, $job, $queue), $transformToString);
}

if ($messageGroupId === null && $isFifo) {
    $messageGroupId = transform($queue, $transformToString);
}
```

Today the method returns early for **string** jobs on standard queues (`if (! $isObject && ! $isFifo) return $options;`). That early return should come **after** the resolver, so string jobs get the default group too. Nothing changes for apps that don't register a resolver.

### Out of scope

`queue:retry` re-pushes failed jobs through `pushRaw()`, which never calls `getQueueableOptions()`. So retried jobs carry no group, with or without a resolver. That is worth a separate issue but should not hold up this one.

## Suggested tests

- No resolver: behaviour is identical to today for object jobs, string jobs, standard and FIFO queues. These are the existing tests.
- A resolver returning a value: an object job without a group gets it, and so does a string job on a standard queue.
- **A job with `onGroup('x')`, a `$messageGroup` property or a `messageGroup()` method keeps its own group. The resolver is not called.**
- A resolver returning `null`: no `MessageGroupId` on a standard queue, and the queue name on a FIFO queue.
- `bulk()` / `SendMessageBatch` entries get the resolved group, because `prepareSendMessageBatchEntry()` already goes through `getQueueableOptions()`.
- `resolveMessageGroupUsing(null)` resets it.

## Downstream workaround (current Advising App code)

`App\Queue\TenantFairSqsQueue::getQueueableOptions()`:

```php
$options = parent::getQueueableOptions($job, $queue, $payload, $delay);

if (isset($options['MessageGroupId'])) {
    return $options;
}

$tenantId = Tenant::current()?->getKey();

if ($tenantId === null) {
    return $options;
}

$options['MessageGroupId'] = (string) $tenantId;

return $options;
```

- **Order:** it runs **after** the parent, so a group set on the job always wins. That is the same precedence as the proposed API.
- **String jobs:** it also gives string jobs on standard queues the tenant group, which is why the proposal moves the early return.
- **Tests:** `tests/Landlord/Queue/TenantFairSqsQueueTest.php`:
    - "tags jobs pushed from a tenant context with the tenant as the message group";
    - "keeps the message group set on the job";
    - "does not set a message group on jobs pushed outside a tenant context";
    - "tags every job in a batch with the tenant as the message group".

## Consuming-app cleanup (after this ships)

> **Implementing the upstream feature? Ignore this section.** It lists Advising App changes to make **after** the feature is released and we upgrade. They are not part of the framework change.

1. Run `pls exec app composer update laravel/framework` to get a release that contains the resolver. The constraint is `^13.0`, so no `composer.json` change is needed unless it only ships in a new major.
2. Delete `App\Queue\TenantFairSqsQueue::getQueueableOptions()`, and the `App\Models\Tenant` import if nothing else in the class uses it.
3. Register the default in `App\Providers\QueueServiceProvider::boot()`, adjusted to the final upstream API:

    ```php
    SqsQueue::resolveMessageGroupUsing(fn (): ?string => Tenant::current()?->getKey());
    ```

4. **Keep the four tests above unchanged.** They now cover the resolver registration end to end, and they must still pass as they are. Pay particular attention to "keeps the message group set on the job", which proves the job still overrides the default. The tests construct `TenantFairSqsQueue` directly, which still works because the static resolver applies to subclasses.
5. If upstream did **not** move the string-job early return, string jobs pushed from a tenant context lose the tenant group. We don't push string jobs, so this is acceptable, but note it in the PR.
6. Update the wording that describes the subclass default:
    - the class docblock on `App\Queue\TenantFairSqsQueue`;
    - the "SQS Configuration" comment in `config/queue.php`;
    - the "Decision Outcome" bullet in `docs/explanations/decisions/0002-static-sqs-fair-queues-for-tenant-isolation.md` ("we default it to the current tenant in a small `SqsQueue` subclass").

    All three should now say the default is registered through Laravel's resolver.

7. Delete this document.

## Suggested PR title & description

**Title:** [13.x] Add a default message group resolver to the SQS queue

**Description:**

> Standard SQS queues support fair queuing: messages carry a `MessageGroupId` (typically a tenant id), and SQS keeps one group's backlog from delaying the others. Laravel already sends `MessageGroupId` on standard queues when a job defines a message group. But multi-tenant apps want every job grouped by tenant, and today that means touching every job or overriding `SqsQueue::getQueueableOptions()`.
>
> This adds `SqsQueue::resolveMessageGroupUsing(?callable)`. The resolver is only a default:
>
> - a group set on the job (`onGroup()`, `$messageGroup` or `messageGroup()`) still wins;
> - FIFO queues still fall back to the queue name when the resolver returns `null`;
> - string jobs on standard queues also get the default.
>
> Nothing changes for apps that don't register a resolver. Tests cover precedence, `null`, FIFO, string jobs, batches and reset.

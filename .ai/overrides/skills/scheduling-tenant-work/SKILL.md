---
name: scheduling-tenant-work
description: "Use when adding to or changing this app's scheduler — app/Console/Kernel.php or a *ForEachTenant orchestrator job. Trigger whenever you schedule recurring work (especially work that must run per tenant), add or edit a job extending App\\Jobs\\DispatchForEachTenant, register a $schedule->job()/command()/call() entry, or schedule a vendor/queued job at the landlord level. Covers the flat landlord-level fan-out model, the DispatchForEachTenant abstract class and its jobForTenant() contract, parameterized orchestrators with uniqueId(), the onOneServer mutex-name collision trap for same-class entries, the tenant-aware-job-deleted-at-the-landlord-level trap (config/multitenancy.php not_tenant_aware_jobs), and the spatie schedule-monitor monitorName() convention. Do not use for writing the per-tenant business logic itself, non-scheduled jobs, general tenant-aware queue mechanics, or writing tests (use writing-tests)."
user-invocable: false
license: Elastic-2.0
metadata:
    author: canyongbs
---

# Scheduling Tenant Work

The scheduler runs **once at the landlord level**. It does **not** loop over tenants, does **not** use `tenants:artisan`, and does **not** schedule TenantAware (`--tenant=*`) commands. An earlier design scheduled every task per tenant, which produced hundreds of schedule entries and made newer tenants wait behind older ones. The current model is a flat schedule of **fan-out orchestrators**: each is dispatched once, then it enqueues one child job per eligible tenant so all tenants are processed in parallel by workers.

## The model

- `app/Console/Kernel.php` holds a **flat** list of `$schedule->job(...)`, `$schedule->command(...)`, and `$schedule->call(...)` entries. Almost every entry is `->onOneServer()` so cluster-wide work runs once — the **one deliberate exception** is the schedule liveness beacon (see trap 3).
- Every entry also gets a stable `->monitorName(...)` (spatie/laravel-schedule-monitor). Never put a tenant id or domain in a name — names must be the same on every run.
- Per-tenant work is expressed as an **orchestrator** — a job extending `App\Jobs\DispatchForEachTenant` — scheduled once. The orchestrator fans out to per-tenant child jobs; it never contains the business logic itself.
- Landlord-level commands (e.g. the health heartbeats and the `MonitoredScheduledTaskLogItem` prune) run **once**, not per tenant.

## Adding per-tenant scheduled work

1. Create a child job that does the actual per-tenant work (a normal tenant-aware `ShouldQueue` job — no special base class). Put it in the owning module's `src/Jobs/`. Do **not** create a TenantAware artisan command for it — calling one without `--tenant` loops **all** tenants.
2. Create an orchestrator in the same module's `src/Jobs/` extending `App\Jobs\DispatchForEachTenant` and implement `jobForTenant()`:

```php
use App\Jobs\DispatchForEachTenant;
use App\Models\Tenant;

class DispatchPruneWidgetsForEachTenant extends DispatchForEachTenant
{
    protected function jobForTenant(Tenant $tenant): ?object
    {
        // Return null to skip this tenant (e.g. an addon/feature is off).
        return new PruneWidgets();
    }
}
```

3. Register it flat in `Kernel::schedule()`:

```php
$schedule->job(new DispatchPruneWidgetsForEachTenant())
    ->hourly()
    ->onOneServer()
    ->monitorName('Dispatch Prune Widgets For Each Tenant');
```

That is the whole pattern. Do not add a tenant loop, `tenants:artisan`, or per-tenant scheduling.

### What the base class already handles — don't re-implement it

`DispatchForEachTenant` is `NotTenantAware`, `ShouldQueue`, and `ShouldBeUnique`. Its `handle()`:

- scopes tenants with `SetupIsComplete` + `ExcludeExpiredSubscriptions` and cursors them (one row at a time);
- wraps each tenant in its own `try/catch` and `report()`s failures, so one tenant cannot break the run;
- calls `jobForTenant($tenant)` inside `$tenant->execute(...)` and dispatches the returned job **while the tenant is current**, so the child job is tagged with that tenant and runs tenant-aware on a worker;
- sets `onQueue(config('queue.landlord_queue'))` and a `uniqueFor` safety ceiling in its constructor.

Your `jobForTenant()` only decides **what** (if anything) to dispatch for a tenant. Return `null` to skip.

### Wrapping an artisan command or model pruning

- To run an existing (non-TenantAware) artisan command per tenant, make the child job use `App\Jobs\Concerns\RunsArtisanCommand` (see `RunHealthChecks` / `PruneStaleCacheTags`) — it throws `ArtisanCommandFailedException` on a non-zero exit.
- Tenant model pruning lives in `App\Jobs\PruneModels::pruners()`. To prune a new tenant model, add it there — do not schedule `model:prune`.

### Parameterized orchestrators (same class, several cadences)

When one orchestrator class runs at multiple frequencies, take the parameter in the constructor, call `parent::__construct()`, and give each a distinct uniqueness lock via `uniqueId()`:

```php
public function __construct(public ReportFrequency $frequency)
{
    parent::__construct();
}

public function uniqueId(): string
{
    return $this->frequency->value;
}
```

## Three traps (each costs real debugging time)

### 1. `onOneServer` mutex-name collision on same-class entries

`Schedule::job()` derives the `onOneServer` mutex from the job's **class name** (its description). Two scheduled entries of the **same class** therefore share one mutex and only one will run. Give every same-class entry a **distinct `->name(...)`** (and a distinct `->monitorName(...)`).

### 2. Tenant-aware jobs dispatched at the landlord level are **deleted**

This app sets `multitenancy.queues_are_tenant_aware_by_default = true`. Any queued job **without** the `NotTenantAware` interface that is dispatched with **no current tenant** (which is exactly the landlord scheduler context) is **deleted before `handle()` runs** by `MakeQueueTenantAwareAction`, so its work silently never happens.

- **Orchestrators are safe** — `DispatchForEachTenant` implements `NotTenantAware`.
- **Child jobs are safe** — they're dispatched inside `$tenant->execute()`, so they carry a tenant.
- **A vendor/queued job you schedule directly at the landlord level is NOT safe.** Register it in `config/multitenancy.php` under `not_tenant_aware_jobs` (this is how `Spatie\Health\Jobs\HealthQueueJob`, dispatched by `health:queue-check-heartbeat`, runs at the landlord level and writes the shared `health` cache store). After changing that config locally, restart the worker container — it holds the config it booted with.

### 3. The schedule liveness beacon must **not** use `onOneServer`

The final entry — `$schedule->call(fn () => touch(storage_path('framework/schedule-heartbeat')))->name('Schedule Liveness Beacon')` — is deliberately **not** `->onOneServer()`, and this is the one exception to the rule above. Each running scheduler task's container healthcheck reads **its own** heartbeat file, so every scheduler node must touch it every minute. `onOneServer` would leave the other nodes' heartbeat files stale and fail their healthchecks. It is also `->doNotMonitor()` (it runs on every node) and reports to Sentry via `->sentryMonitor(...)`. Do not "fix" a review flag by adding `onOneServer` here.

## Verifying

- Run the app in Docker: prefix commands with `pls exec app`.
- `pls exec app php artisan schedule:list` — confirm the flat schedule and that same-class entries have distinct names.
- Cover new cadences in `tests/Landlord/Console/KernelTest.php` (it pins the minute/15-minute/hourly/daily/monthly cadences via `schedule:run`). Give each orchestrator its own `<module>/tests/Landlord/Jobs/*ForEachTenantTest.php` that calls `->handle()` and asserts the child job is pushed once per eligible tenant. `$schedule->command(...)` entries run in a subprocess, so their dispatches are invisible to `Queue::fake()` — assert their redis side effects instead. Follow the `writing-tests` skill.

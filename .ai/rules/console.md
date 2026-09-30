---
paths:
    - app/Console/Kernel.php
---

# Console

## Scheduler is flat and landlord-level; per-tenant work fans out

Never loop tenants, use `tenants:artisan`, or schedule TenantAware (`--tenant`) commands here. Per-tenant recurring work = an orchestrator extending `App\Jobs\DispatchForEachTenant`, scheduled once with `->onOneServer()` and a stable `->monitorName()` (no tenant ids/domains). Traps: (1) same-class entries share the `onOneServer` mutex — give each a distinct `->name()`; (2) a queued job without `NotTenantAware` scheduled at the landlord level is deleted before `handle()` — add vendor jobs to `config/multitenancy.php` `not_tenant_aware_jobs`; (3) the `Schedule Liveness Beacon` is per-node — never add `onOneServer` to it. Full how-to: `scheduling-tenant-work` skill.

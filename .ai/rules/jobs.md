---
paths:
    - '**/Jobs/Dispatch*ForEachTenant.php'
---

# Jobs

## Fan-out orchestrators only choose the child job

Orchestrators extend `App\Jobs\DispatchForEachTenant` and only implement `jobForTenant()` (return the child job, or `null` to skip). Don't re-implement tenant scoping, per-tenant try/catch, landlord queue, or uniqueness — the base class does it. Parameterized orchestrators must call `parent::__construct()` and override `uniqueId()`. Put business logic in the child job, never the orchestrator. See the `scheduling-tenant-work` skill.

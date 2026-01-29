# Feature Flags

Feature Flags are used in our system to control the execution of logic that, for various reasons, should either not run or execute differently in particular scenarios.

## Zero-Downtime Deployments

The most common scenario is to allow our application to deploy with zero downtime to all our Tenants and Users, and to perform migrations on Tenants without failures in one Tenant affecting the others. We accomplish this by deploying our code and making it available without enabling any kind of maintenance mode and before migrations are run.

To prevent errors or other issues, we apply Feature Flags to areas that require the migrations to have already been completed. For example:

```php
if (FeatureFlagClass::active()) {
    // The feature flag is active, so we know the database schema and data migrations have run, and the new logic is executed here.
} else {
    // Old logic that does not require any changes from schema or data migrations is executed here.
}
```

Any change to the database schema or data that would cause issues if the code were accessed and executed before the migrations finished must have a Feature Flag to prevent issues from occurring. This can be done either by preventing the new code from running at all or by using the old code if it is not yet active.

## Feature Flag Lifecycle

Feature Flags in our system are temporary by design. They exist to bridge the gap between code deployment and migration completion. Once the deployment containing your changes has gone out and successfully executed, there will generally be a task in the next release cycle to remove the Feature Flag.

The Feature Flag will be purged from the database automatically if the class is no longer present in the codebase.

---

See [Manage Feature Flags](../how-tos/manage-feature-flags.md) for practical guidance on creating and cleaning up Feature Flags.

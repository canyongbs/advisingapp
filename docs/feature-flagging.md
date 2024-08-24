# Feature Flagging

## Introduction

Feature Flags are used in our system to control the execution of logic that, for various reasons, should either not run or execute differently in particular scenarios.

The most common scenario is to allow our application to deploy with zero downtime to all our Tenants and Users, and to perform migrations on Tenants without failures in one Tenant affecting the others. We accomplish this by deploying our code and making it available without enabling any kind of maintenance mode and before migrations are run.

To prevent errors or other issues, we apply Feature Flags to areas that require the migrations to have already been completed. For example:

```php
if (FeatureFlag::SomeFeature->active()) {
    // The feature flag is active, so we know the database schema and data migrations have run, and the new logic is executed here.
} else {
    // Old logic that does not require any changes from schema or data migrations is executed here.
}
```

## Creating a New Feature Flag

Any change to the database schema or data that would cause issues if the code were accessed and executed before the migrations finished must have a Feature Flag to prevent issues from occurring. This can be done either by preventing the new code from running at all or by using the old code if it is not yet active.

New Feature Flags should be added as a case to the `App\Enums\FeatureFlag` ENUM. This ENUM contains many helpers for managing Feature Flags, such as activating, deactivating, and checking the status of a Feature Flag.

After you have created all your schema and data migrations, you should create a new data migration specifically to activate the Feature Flag. For example:

```php
use App\Enums\FeatureFlag;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        FeatureFlag::SisIntegrationSettings->activate();
    }

    public function down(): void
    {
        FeatureFlag::SisIntegrationSettings->deactivate();
    }
};
```

Optionally, you can choose to add a custom match in the definition of the `App\Enums\FeatureFlag` ENUM to activate the Feature Flag if certain conditions are met, such as checking if certain tables or columns exist, or if certain migrations have run, etc. However, you still MUST create the activation migration to ensure it gets activated, just in case someone executes code that checks the feature, parses the definition, and has marked it as deactivated.

## Feature Flag cleanup

After the deployment containing your new changes and Feature Flag has gone out and successfully executed, there will generally be a task in the next release cycle to remove the Feature Flag.

To remove the Feature Flag, you should delete all references to its case, adjust any logic to work as if the Feature Flag were active, and delete any unneeded legacy code. You should then also delete the activation migration, and finally, remove the case from the `App\Enums\FeatureFlag` ENUM.

The Feature Flag will be purged from the database automatically if it is no longer present in the ENUM.

### Additional Details

See also [Data Migrations](./data-migrations.md)
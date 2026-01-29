# Manage Feature Flags

This guide covers creating, activating, and cleaning up Feature Flags in the application.

For background on why Feature Flags are used, see [Feature Flags](../explanations/feature-flags.md).

---

## Creating a Feature Flag

Generate a new class-based Feature Flag using the artisan command:

```bash
php artisan pennant:feature [The name you want for your Feature Flag class like SomeFeature]
```

This class will contain helpers for managing Feature Flags, such as activating, deactivating, and checking the status. It extends a base abstract class and will automatically be registered in the container, making it immediately available for use.

In most cases the Feature Flag class can be left as generated. Its `resolve` method will return `false`, causing it to be deactivated until specifically activated by a data migration.

Optionally, you can add logic to `resolve` and return `true` to activate the Feature Flag if certain conditions are met, such as checking if certain tables or columns exist, or if certain migrations have run. However, you still **must** create the activation migration to ensure it gets activated, just in case someone executes code that checks the feature, parses the definition, and has marked it as deactivated.

---

## Activating a Feature Flag

After you have created all your schema and data migrations, create a new data migration specifically to activate the Feature Flag:

```php
use App\Features\SomeFeature;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    public function up(): void
    {
        SomeFeature::activate();
    }

    public function down(): void
    {
        SomeFeature::deactivate();
    }
};
```

---

## Cleaning up a Feature Flag

After the deployment containing your new changes and Feature Flag has gone out and successfully executed, there will generally be a task in the next release cycle to remove the Feature Flag.

To remove a Feature Flag:

- Delete all references to the Feature Flag throughout the codebase
- Adjust any logic to work as if the Feature Flag were active
- Delete any unneeded legacy code
- Delete the activation migration
- Delete the Feature Flag class

The Feature Flag will be purged from the database automatically if it is no longer present.

---

See also [Data Migrations](../data-migrations.md)

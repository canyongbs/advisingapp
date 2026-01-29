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

There are two options for activating a Feature Flag during migrations.

### Preferred: Activate Within the Same Migration

The preferred approach is to activate the Feature Flag within the same migration that makes the schema or data changes. Wrap both the changes and the activation in a database transaction for safety—this ensures that if either the migration or the activation fails, both are rolled back together.

```php
use App\Features\SomeFeature;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        DB::transaction(function () {
            Schema::table('some_table', function (Blueprint $table) {
                $table->string('new_column')->nullable();
            });

            SomeFeature::activate();
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            SomeFeature::deactivate();

            Schema::table('some_table', function (Blueprint $table) {
                $table->dropColumn('new_column');
            });
        });
    }
};
```

### Alternative: Activate in a Separate Migration

If you have multiple schema or data migrations that need to complete before the Feature Flag should be activated, create a separate migration that runs after all the pertinent migrations:

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

Ensure this migration is timestamped to run after all related schema and data migrations.

---

## Post-Deployment Cleanup

After deployment, someone will be tasked with cleaning up the Feature Flag. Make their job easier by documenting what needs to change.

### Simple Cases

In most cases, clean up is straightforward—search for usages of the Feature Flag class and:

- Remove the Feature Flag conditional
- Keep the code that runs when the flag is active
- Delete the code that runs when it's inactive

### Complex Cases

When cleanup requires more than simply removing conditionals, add comments to guide the person doing the cleanup. Use this pattern:

```php
// TODO: FeatureFlag Cleanup - Details on what changes should be made
```

Multiline comments can be used for more detailed instructions or code examples:

```php
/*
 * TODO: FeatureFlag Cleanup - After SomeFeature is removed:
 * - Change this default value from 'legacy' to 'new_format'
 * - Update the config in config/app.php to set 'feature_mode' => true
 * - Remove the fallback query below
 */
```

Adapt the comment syntax for the language of the file (e.g., `<!-- TODO: FeatureFlag Cleanup -->` in Blade templates).

This pattern makes it easy to search the codebase for `TODO: FeatureFlag Cleanup` to find all locations that need attention, without relying on filenames or line numbers that may change.

---

## Cleaning up a Feature Flag

After the deployment containing your new changes and Feature Flag has gone out and successfully executed, there will generally be a task in the next release cycle to remove the Feature Flag.

To remove a Feature Flag:

- Search the codebase for `TODO: FeatureFlag Cleanup` to find any documented cleanup tasks
- Delete all references to the Feature Flag throughout the codebase
- Adjust any logic to work as if the Feature Flag were active
- Delete any unneeded legacy code
- Delete the activation migration
- Delete the Feature Flag class

The Feature Flag will be purged from the database automatically if it is no longer present.

---

See also [Data Migrations](./data-migrations.md)

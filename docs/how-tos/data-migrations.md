# Data Migrations

This guide covers creating and managing migrations that include data changes.

---

## Introduction

Migrations in the application can contain schema changes, data changes, or a mixture of both. There is no requirement to separate schema and data changes into different migration files—use your judgment based on what makes sense for the change being made.

Data changes in migrations are typically used to:

- Migrate data from one format to another
- Perform data clean up operations
- Seed initial data for new features
- Activate Feature Flags after schema changes

Migrations created in the `database/migrations` directory are automatically run on all Tenants. Migrations created in the `database/landlord` directory are run only on the Landlord database.

---

## Permanent vs Temporary Migrations

### Permanent Migrations

Permanent migrations remain in the codebase indefinitely. These are the standard migrations that define the evolution of the database schema and data over time.

When writing permanent migrations that include data changes, adhere to the following rules:

- Do **NOT** use references to classes and facades that could be removed in the future. This includes Eloquent models. The only accepted classes to use within permanent migrations are:
    - `Illuminate\Support\Facades\DB` — to interact with the database
    - Feature Flag classes located within the `App\Features` namespace extending `App\Support\AbstractFeatureFlag` — to interact with feature flagging
- Ensure all possible SQL errors are captured and handled, such as `UniqueConstraintViolationException`. Wrap database changes in a transaction to prevent corruption of the query connection.
- Ensure that the migration is idempotent. This means the migration can be run multiple times without causing issues. Check for table and column existence, catch and handle SQL errors properly, and verify data is in the expected state before changing it.
- You **MUST** include a `down` method in the migration.
    - In production, migrations are intended to be run once and not rolled back. If you need to fix a migration that has already run in production, create a new migration to do so.
    - The `down` method is required to facilitate testing of migrations in special cases.

### Temporary Migrations

Temporary migrations are one-time migrations (or portions of migrations) that will be deleted after they have been run across all environments. These are useful for:

- Seeding data for existing tenants when introducing a new feature
- One-time data clean up or transformation tasks
- Back filling data that only needs to happen once

**If the entire migration file should be deleted after it has run**, prefix the migration name with `tmp_`:

```bash
php artisan make:migration tmp_backfill_user_preferences
```

Temporary migrations **may** use Eloquent models and other classes that could be removed in the future, since the migration itself will be deleted. However, they should still:

- Be wrapped in transactions for safety
- Include a `down` method
- Be idempotent where possible
- Though using Eloquent Models is allowed in temporary migrations, it is highly recommended that they be used very sparingly, if at all. If you choose to use them you MUST consider and possibly mitigate side effects of their use such as observers, global scopes on the model, etc.

---

## Creating a Migration

Create a migration using the standard Laravel Artisan command:

```bash
php artisan make:migration add_status_column_to_orders_table
```

For temporary migrations that will be deleted after running:

```bash
php artisan make:migration tmp_seed_default_settings_for_existing_tenants
```

---

## Running Migrations

Migrations can be run using the `migrate` Artisan command.

To run migrations on the Landlord database:

```bash
php artisan migrate --database=landlord --path=database/landlord
```

To run migrations on all Tenants:

```bash
php artisan tenants:artisan "migrate"
```

---

See also:

- [Manage Feature Flags](./manage-feature-flags.md) — for activating Feature Flags within migrations
- [Creating Permissions](./creating-permissions.md) — for permission-related migrations

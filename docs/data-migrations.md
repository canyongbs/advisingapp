# Data Migrations

Data migrations in the application are created, stored, and executed much in the same way as Laravel schema migrations.

## Introduction

Data migrations are used to perform one-time operations on the database. This can be used to migrate data from one format to another, or to perform data cleanup operations.

Data migrations are contained in typical Laravel migration files. But contain a particular prefix to differentiate them from schema migrations. And are subject to a specific set of rules.

## Usage

### Creating Operations

A data migration can be created using the `make:migration` Artisan command as is typical with Laravel migrations. Please prepend the name of the migration with `data_` to differentiate it from schema migrations.

Migrations created in the `database/migrations` directory are automatically run on all Tenants. Whereas the migrations created in the `database/landlord` directory are run only on the Landlord database.

For example:

```bash
php artisan make:migration data_change_name_in_users_table
```

When creating data migrations it is important to adhere to the following rules:

1. Do **not** use references to classes and facades that could be removed in the future. This includes Eloquent models. Instead, use the `Illuminate\Support\Facades\DB` facade to interact with the database. You may also access the `Laravel\Pennant\Feature` class to interact with feature flagging.
2. Ensure that the migration is idempotent. This means that the migration can be run multiple times without causing any issues. Tables and columns should be checked for the existence, data checked if it is in the expected state before it is changed, etc.
3. Do **not** include a `down` method in the migration. Data migrations are intended to be run once and not rolled back. If you need to roll back a data migration, you should create a new data migration to do so.
4. Do **not** include schema changes in data migrations. Schema changes should be done in schema migrations. Data migrations should only be used to manipulate data.

### Running Data Migrations

Because data migrations are stored in the same directory as schema migrations, they can be run using the `migrate` Artisan command.

To run migrations from the Landlord database:

```bash
php artisan migrate --database=landlord --path=database/landlord
```

To run migrations on all Tenants:

```bash
php artisan migrate --database=tenant
```
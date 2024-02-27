# Data Migrations

Data migrations in the application is a custom implementation extended from and inspired by [One-Time Operations for Laravel](https://github.com/TimoKoerber/laravel-one-time-operations).

## Introduction

Data migrations are used to perform one-time operations on the database. This can be used to migrate data from one format to another, or to perform data cleanup operations.

Data migrations are contained in single-use classes called "Operations". Each operation is responsible for performing a single task, and is only run once.

There are two types of Operations: "landlord" and "tenant". Landlord operations are run once on the landlord database, while tenant operations are run once for each tenant database.

## Usage

### Creating Operations

In order to create a new data migration operation, you can use the `operations:make` Artisan command:

```bash
php artisan operations:make MyOperation
```

This will create a new operation class in the `app/database/operations` directory. The class will contain a `process` method, which is called when the operation is executed.

It also contains a `type` property, which should be set to either "landlord" or "tenant", depending on where you want the operation to be run.

Further customizable properties are as follows:

- `async`: Whether the operation should be run asynchronously on the queue. Defaults to `true`.
- `queue`: The queue on which the operation should be run. Defaults to `default`.
- `tag` : The tag for the operation, this is optional and can be used to segment operations. Defaults to `null`.

### Running Operations

To run all pending operations, you can use the `operations:process` Artisan command. You must specify the type of operation you want to run, either `landlord` or `tenant`.

```bash
php artisan operations:process landlord
```

After running the command, all pending operations of the specified type will be executed in the order they were created. Either immediately executing or being queued based on the `async` property. It is heavily recommended to run the operations on a queue.

Once dispatched the operation will be required in the respective databases `operations` table. This table is used to keep track of which operations have been run, and to prevent them from being run again.
Upon completion of the operation the records `completed_at` column will be updated to the current timestamp.
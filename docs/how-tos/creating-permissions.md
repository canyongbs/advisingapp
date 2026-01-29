# Creating Permissions

This guide covers creating permissions in the application using Permission Migrations.

This application uses the [Spatie Laravel-permission](https://spatie.be/docs/laravel-permission/v5/introduction) package to facilitate permissions.

---

## Permission Groups

Permission Groups are a labeling system applied to permissions to organize them in the UI. All permissions **must** be assigned to a `PermissionGroup`.

Many Permission Groups already exist, so new permissions can be added to them. Alternatively, a new Permission Group can be created when adding a permission.

Product will typically define what the Permission Group should be called. If not specified, the developer should confirm with leadership before creating a new group.

---

## Allowed Permissions

The application uses a specific set of allowed permissions. When creating permissions for a model or feature, use the following format:

- `permission-name.view-any`
- `permission-name.create`
- `permission-name.*.view`
- `permission-name.*.update`
- `permission-name.*.delete`
- `permission-name.*.restore`
- `permission-name.*.force-delete`

Additionally, when requested:

- `permission-name.import`

Not all permissions are required for every model. For example, if a model should never be updated, do not create the `permission-name.*.update` permission. What permissions should or should not be created should be decided prior to creation. If details are not provided beforehand, it is the developer's responsibility to confirm with Product or leadership.

---

## Permission Migrations

Permissions are created and managed through Permission Migrations. These operate like regular migrations but follow additional conventions:

- Prepend the migration name with `seed_permissions_` to differentiate it from other migrations
- Use the helpers in the `CanyonGBS\Common\Database\Migrations\Concerns\CanModifyPermissions` trait
- Wrap any additional queries in both a database transaction and a `try`/`catch` block to handle SQL errors such as `UniqueConstraintViolationException`

### Creating a Permission Migration

Use the dedicated artisan command to create a Permission Migration:

```bash
php artisan make:permission-migration
```

Example usage:

```bash
php artisan make:permission-migration seed_permissions_add_foo_permissions
```

This command works with the `--module` flag to create the migration in a specific module.

---

See also [Data Migrations](./data-migrations.md)

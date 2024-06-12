# Roles and Permissions
Roles and permissions in the application have been setup in a flexible and maintainable manner, making it easy for application owners to make adjustments to configuration, and ensure that the custom roles and permissions they will need are in place.

This application uses the [Spatie Laravel-permission](https://spatie.be/docs/laravel-permission/v5/introduction) package in order to facilitate the usage of roles and permissions.

The application uses a dedicated convention in order to define and populate roles and permissions, leaving little in the way of thinking when it comes time to stand up the application locally or in a live environment.

### Local Setup
In order to get your local environment correctly set up, you won't have to do anything beyond running the setup process defined in the documentation. Permissions are created in data migrations and roles are created by seeders when a Tenant is created.

## Permissions
Permissions in the application are created and managed through special Data Migrations called Permission Migrations. Details on Data Migrations can be found in the [Data Migrations](/docs/data-migrations.md) documentation.

### Permission Migrations
Permission Migrations operate much the same as regular Data Migrations but adhere to a few slightly different or additional rules:

1. Prepend the name of the migration with `seed_permissions_` to differentiate it from schema migrations and Data Migrations.
2. Use the helpers in the `Database\Migrations\Concerns\CanModifyPermissions` trait.
3. Any other queries you write should be surrounded by both a database transaction and a `try`/`catch` block to catch any SQL errors such as a `UniqueConstraintViolation`.

### What permissions to create

#### Models
Typically new Models added to the application should have the following default permissions created for them:

- `your-model.view-any`
- `your-model.create`
- `your-model.*.view`
- `your-model.*.update`
- `your-model.*.delete`
- `your-model.*.restore`
- `your-model.*.force-delete`

But, for Models, there may be special cases where either additional permissions should be created. Or some permissions should **not** be created. (For example, if it is expected that Model should never be updated, we would not want the `your-model.*.update` update permission)

What permissions should or should not be created should be decided on prior to creation. But if details are not provided before hand, it is the developers resonsobility to ensure discussion on deciding the permissions takes place.

#### Custom Permissions
Sometimes we have a need for permissions that are not neccarily related to Models. We call these "custom permissions". For example, if we are gating access to a certain page or feature based on a custom RBAC setup.

The name of this permission can be virtually anything and should be decided upon by the developer with feedback from the team and Product.

#### Permission Groups
Permission Groups are a labelling system applied to permissions to put them into a grouping for better management and organization in the UI. As such all permissions **MUST** be related to a `PermissionGroup`.

Many `PermissionGroup`s currently exist, so new permissions can be created and added to them. Or when creating a permission an new `PermissionGroup` can be created. What group to assign a permission to should be decided by the developer and/or decided upon by the team and Product.

#### Make Migration Command

This application has a command, virtually the same as the default `make:migration` command to create Permission Migrations.

```bash
php artisan make:permission-migration 
```

Example usage:

```bash
php artisan make:permission-migration seed_permissions_add_foo_permissions
```

This command works with the `--module` flag.

## Roles
Roles are also configurable at the module level. Within configuration files in a module, you can define a Role and the Permissions that it will have.

Roles will inherit their name from the name of their config file, and should be structured like so:

*Directory Structure Example*
```
- my-module
  - config
    - roles
      - web
        - some_role.php
```

Within the role file definition, you can specify the `custom` and `model` permissions that the Role should have:

*some_role.php*
```
<?php

return [
    'custom' => [
        'ability_to_write_docs'
    ],

    'models' => [
        'documentation-model' => [
            '*'
        ],
        'some-other-model' => [
            'view-any'
        ]
    ]
];
```

## Registering Roles and Permissions
In order to register Roles and Permissions, the `authorization` module exposes registries that every other module can interact with. The Advising App platform expects that your Roles and Permissions are defined within configuration files.

The two registries available are:

1. AuthorizationRoleRegistry
2. AuthorizationPermissionRegistry

If a new module has roles and permissions that need to be added to the application, they should be added within the appropriate registry from the Module's service provider. An example of that looks like this:

```
NewModuleServiceProvider class extends ServiceProvider
{
    public function boot(AuthorizationPermissionRegistry $permissionRegistry, AuthorizationRoleRegistry $roleRegistry): void
    {
        $permissionRegistry->registerWebPermissions(
            module: 'new-module',
            path: 'permissions/web/custom'
        );

        $roleRegistry->registerWebRoles(
            module: 'new-module',
            path: 'roles/web'
        );
    }
}
```

The `AuthorizationPermissionRegistry` and `AuthorizationRoleRegistry` expose methods that allow you to register web or api roles and permissions by passing in your module name and the relative config path to your definitions.

By default, the Advising App platform will already take care of adding standardized permissions for every model that your application introduces, so you'll need to ensure you register those models with the `Relation::morphMap()`. So, the only permissions that you need to explicitly define here are custom permissions that are not related to models.

The roles that you add can relate to any model or custom permissions that your or another module defines, and they should follow the convention defined above.

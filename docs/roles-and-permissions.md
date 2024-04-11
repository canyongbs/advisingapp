# Roles and Permissions
Roles and permissions in the application have been setup in a flexible and maintainable manner, making it easy for application owners to make adjustments to configuration, and ensure that the custom roles and permissions they will need are in place.

This application uses the [Spatie Laravel-permission](https://spatie.be/docs/laravel-permission/v5/introduction) package in order to facilitate the usage of roles and permissions.

The application uses a dedicated convention in order to define and populate roles and permissions, leaving little in the way of thinking when it comes time to stand up the application locally or in a live environment. Roles and permissions are mainly controlled through the `authorization` module, but each additional module also has some responsibility in order to correctly configure what it may need to properly facilitate role based access control.

### Local Setup
In order to get your local environment correctly set up, you won't have to do anything beyond running the `composer refresh-database` command. Under the hood, this command will call on the `DatabaseSeeder`, which calls the `SyncRolesAndPermissions` artisan command. This command will seed and sync all of the roles and permissions defined in the application.

## Permissions
The application defines two distinct types of permissions: those related directly to models and those that are not. In the application, these are aptly referred to as `model` permissions and `custom` permissions.

The application defines a sane set of defaults for each model in the application, but also provides extensibility. In order to ensure that any new model introduced in the application automatically has the default `model` permissions defined for it, the following needs to be done:

1. Register the model with the `Relation::morphMap()` in your module service provider
2. Extend the `BaseModel`, or directly use the `AdvisingApp\Authorization\Models\Concerns\DefinesPermissions.php` trait on your model.

Doing the following will ensure your model gets the following permission definitions:

- 'your-model.view-any'
- 'your-model.create'
- 'your-model.*.view'
- 'your-model.*.update'
- 'your-model.*.delete'
- 'your-model.*.restore'
- 'your-model.*.force-delete'

As stated earlier, the application offers flexibility to override or extend this pattern. For example, if a particular model did not abide by typical CRUD conventions, and instead just needed a permission that defined the ability to "export", we could define the following method on that model:

```
public function getWebPermissions(): Collection
{
    return collect(['*.export']);
}
```

This would override application defaults and just define this single permission for whatever model this method existed on. If we just wanted to extend the existing functionality, and add this permission *in addition to* all of the existing defaults, our extending method could look like this:

```
public function getWebPermissions(): Collection
{
    return collect(['*.export', ...$this->webPermissions()]);
}
```

## Roles
Similar to permissions, Roles are also configurable at the module level. Within configuration files in a module, you can define a Role and the Permissions that it will have.

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

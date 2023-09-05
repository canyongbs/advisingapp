# Roles and Permissions
Roles and permissions in the application have been setup in a flexible and maintainable manner, making it easy for application owners to make adjustments to configuration, and ensure that the custom roles and permissions they will need are in place.

This application uses the [Spatie Laravel-permission](https://spatie.be/docs/laravel-permission/v5/introduction) package in order to facilitate the usage of roles and permissions.

The application uses a dedicated convention in order to define and populate roles and permissions, leaving little in the way of thinking when it comes time to stand up the application locally or in a live environment. Roles and permissions are mainly controlled through the `authorization` module, but each additional module also has some responsibility in order to correctly configure what it may need to properly facilitate role based access control.

### Local Setup
In order to get your local environment correctly set up, you won't have to do anything beyond running the `composer refresh-database` command. Under the hood, this command will call on the `DatabaseSeeder`, which calls the `SyncRolesAndPermissions` artisan command. This command will seed and sync all of the roles and permissions defined in the application.

## Role Groups
Role Groups are a key component to understand in this application, as they are more akin to what we typically think of as Roles.

In order to make the assignment and management of roles simple, Role Groups are used as logical groupings of Roles, which are even smaller logical groupings containing Permissions.

A Role can belong to many Role Groups, and a User can belong to many Role Groups. When a User is assigned to a Role Group, they inherit all of the Roles that are attached to that Role Group. Any time a Role is added to a Role Group, any User that belongs to the Role Group will receive access to the Role that was added.

> In order for a User to exist in a Role Group, they **must** have every Role defined in the Role Group.

This means that if a Role is removed from a User, but that Role belongs to a Role Group that the User is also in, the User will lose access to the Role Group, and subsequently any other Roles that the Role Group had attached

*Note: There is a planned improvement in the works which would allow an administering user to "directly" apply the remaining Roles of a Role Group to a user in the event they just want to remove a single Role.*

A good example of how Role Groups are used in the application and how they interact with Roles and Permissions can be understood as follows.

The Assist application provides a model called ServiceRequest. This represents service requests opened and managed within the Assist system. Associated with this model are many permissions pertaining to the viewing, creating, updating, and deleting of cases. In order to group these permissions, we may want to create a "Case Manager" Role. This Role can be directly applied to a User, but it may also be applied to a User through a Role Group.

We can create a Role Group called "Administrator", which could receive the "Case Manager" Role, as well as the "Knowledge Base Manager" Role, among others. Then, when a new administrator is added to the system, they can simply be granted the "Administrator" Role Group in order to inherit all of the roles and subsequent permissions that fall into this grouping.

There are some high level rules about Role Groups that should be understood.

1. When a user is assigned to a RoleGroup, any Role within this group that the User has *not already been assigned* will be assigned to the User, and will be denoted by the `via: role_group` pivot attribute.
2. When a Role is assigned to a RoleGroup, any User within this group will be assigned the new Role *if they have not already been assigned*. If a Role was previously directly assigned, it will not be overwritten when the Role is added to the RoleGroup.
3. When removing a Role from a User, if it belongs to a RoleGroup, the User will be removed from the RoleGroup entirely. A User cannot exist as a member of a RoleGroup without *all* of the Roles that exist within it.
4. When a Role is removed from a RoleGroup, that Role is removed from any User in the RoleGroup, who had that Role assigned to them via the RoleGroup. If a Role exists within multiple RoleGroups to which a user is assigned, but the Role is only removed from one, the User will keep that Role via the RoleGroup that still contains the Role.

## Permissions
The application defines two distinct types of permissions: those related directly to models and those that are not. In the application, these are aptly referred to as `model` permissions and `custom` permissions.

The application defines a sane set of defaults for each model in the application, but also provides extensibility. In order to ensure that any new model introduced in the application automatically has the default `model` permissions defined for it, the following needs to be done:

1. Register the model with the `Relation::morphMap()` in your module service provider
2. Extend the `BaseModel`, or directly use the `Assist\Authorization\Models\Concerns\DefinesPermissions.php` trait on your model.

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

1. There are two ways roles can be assigned:
   1. "directly" by simply assigning a single Role to a User
   2. "role_group" by assigning a RoleGroup to a User, wherein they inherit all of the Roles that belong to this group

## Registering Roles and Permissions
In order to register Roles and Permissions, the `authorization` module exposes registries that every other module can interact with. The Assist platform expects that your Roles and Permissions are defined within configuration files.

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

By default, the Assist platform will already take care of adding standardized permissions for every model that your application introduces, so you'll need to ensure you register those models with the `Relation::morphMap()`. So, the only permissions that you need to explicitly define here are custom permissions that are not related to models.

The roles that you add can relate to any model or custom permissions that your or another module defines, and they should follow the convention defined above.

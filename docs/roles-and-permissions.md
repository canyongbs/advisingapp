# Roles and Permissions
Roles and permissions in the application have been setup in a flexible and maintainable manner, making it easy for application owners to make adjustments to configuration, and ensure that the custom roles and permissions they will need are in place.

This application uses the [Spatie Laravel-permission](https://spatie.be/docs/laravel-permission/v5/introduction) package in order to facilitate the usage of roles and permissions.

The application uses a dedicated convention in order to define and populate roles and permissions, leaving little in the way of thinking when it comes time to stand up the application locally or in a live environment. Roles and permissions are mainly controlled through the `authorization` module, but each independent module also has responsibilities in order to correctly configure what it will need. You can find more on that here (TODO link to module section)

## Local Development
In order to get your local environment correctly set up, you won't have to do anything beyond running the `composer refresh-database` command. Under the hood, this command will call on the `DatabaseSeeder`, which calls the `SyncRolesAndPermissions` artisan command, which will seed and sync all of the roles and permissions defined in the application. Currently by default, no Role Groups are created when running this command.

## Role Groups
Role Groups are a key component to understand in this application, as they are more akin to what we typically think of as roles.

In order to make the assignment and management of roles simple, role groups are used as logical groupings of roles, which are even smaller logical groupings containing permissions.

A Role can belong to many Role Groups, and a User can belong to many Role Groups. When a User is assigned to a Role Group, they inherit all of the Roles that are attached to that Role Group. Any time a Role is added to a Role Group, any User that belongs to the Role Group will receive access to the Role that was added.

In order for a User to exist in a Role Group, they **must** have every Role defined in the Role Group. Because controls are set up fairly granularly, an administering user does have the ability to remove a single Role from a User. But, in the event of this happening, the User will be removed from every Role Group which this Role potentially belonged, subsequently resulting in the User also losing access to all of the Roles from those Role Groups.

*Note: There is a planned improvement in the works which would allow an administering user to "directly" apply the remaining Roles of a Role Group to a user in the event they just want to remove a single Role.*

A good example of how Role Groups are used in the application and how they interact with Roles and Permissions can be understood as follows.

The Assist application provides a model called Case Item. This represents cases opened and managed within the Assist system. Associated with this model are many permissions pertaining to the viewing, creating, updating, and deleting of cases. In order to group these permissions, we may want to create a "Case Manager" Role. This Role can be directly applied to a User, but it may also be applied to a User through a Role Group.

An example of a Role Group could be something like "Administrator", which could receive the "Case Manager" Role, as well as the "Knowledge Base Manager" Role, among others. Then, when a new administrator is added to the system, they can simply be granted the "Administrator" role group in order to inherit all of the roles and subsequent permissions that fall into this grouping.

There are some high level rules about Role Groups that should be understood.

1. When a user is assigned to a RoleGroup, any Role within this group that the User has *not already been assigned* will be assigned to the User, and will be denoted by the `via: role_group` pivot attribute.
2. When a Role is assigned to a RoleGroup, any User within this group will be assigned the new Role *if they have not already been assigned*. If a Role was previously directly assigned, it will not be overwritten when the Role is added to the RoleGroup.
3. When removing a Role from a User, if it belongs to a RoleGroup, the User will be removed from the RoleGroup entirely. A User cannot exist as a member of a RoleGroup without *all* of the Roles that exist within it. When doing this removal through the UI, the administrating user is asked if they want to apply the rest of the existing Roles directly to the user, so they can potentially keep the rest of the Roles of that RoleGroup.
4. When a Role is removed from a RoleGroup, that Role is removed from any User in the RoleGroup, who had that Role assigned to them via the RoleGroup. If a Role exists within multiple RoleGroups to which a user is assigned, but the Role is only removed from one, the User will keep that Role via the RoleGroup that still contains the Role.

## Permissions
The application defines two distinct types of permissions: those related directly to models and those that are not. In the application, there are referred to aptly as `model` permissions and `custom` permissions.

The application defines a sane set of defaults for each model in the application, but also provides extensibility. For example, if a particular model did not abide by typical CRUD conventions, and instead needed a permission that defined the ability to export, we could define the following method on that model:

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
    return collect(['*.inpsect', ...$this->webPermissions()]);
}
```

### Convention

In order to adhere to the conventions of the application, all new permissions should be defined in the appropriate `config` file. If you inspect the `config` directory, you will observe the following directory structure:

- config
  - permissions
    - api
      - custom.php
      - model.php
    - web
      - custom.php
      - model.php

This structure allows us to easily see the defaults of the application, and determine exactly what is provided to us in terms of custom and model permissions.

## Roles
Similar to permissions, every initial role made available by the application can be found in the `config/roles` directory.

Every initial role that the application has is defined in the `config/roles` directory. The file name represents the name of the role, and the permissions provided to the role are defined within the `custom` and `model` keys as described in the Permissions convention.

<!-- TODO Re-write the package/modules section as this is no longer correct -->
<!-- Module definitions should exist within a module -->

1. There are two ways roles can be assigned:
   1. "directly" by simply assigning a single Role to a User
   2. "role_group" by assigning a RoleGroup to a User, wherein they inherit all of the Roles that belong to this group
## Packages/Modules
**Future Feature*

In order to faciliate the addition of permissions specific to new modules or packages that are introduced to the application, they should adhere to the convention established by the custom application.

When a new package or module wants to introduce permissions, it should model its configuration in the same way the application's configuration is constructed, and publish its permission configuration to `config/permissions/packages` and its role configuration to `config/roles/packages`.

An example of what this would like like for the development of a package, `package-a`:

*Permissions*
- config
  - permissions
    - packages
      - package-a
        - api
          - custom.php
          - model.php
        - web
          - custom.php
          - model.php

*Roles*
- config
  - roles
    - packages
      - package-a
        - api
          - new_role.php
        - web
          - new_role.php

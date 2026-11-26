---
paths:
    - '{app,app-modules}/**'
---

# Authorization

## A user's roles include the roles of their groups

A user's effective roles are their direct roles plus the non-admin roles of their user groups (`UserGroup::roles()` excludes admin roles). Read them through Spatie's methods and scopes on `User` (`hasRole()`, `getRoleNames()`, `can()`, `User::role()`, `User::permission()`, etc.) or `User::getEffectiveRoles()`, which are overridden to include group roles. Never check authorization against `$user->roles`, `roles()`, or `whereHas('roles')`: they only hold direct roles, because `roles()` is the write relation for `assignRole()` and `syncRoles()`. The admin checks on `Authenticatable` (`isAdmin()`, `isSuperAdmin()`, etc.) are the exception: admin roles are never inherited, so they read the direct roles without loading groups. When Spatie adds a method that reads roles, override it on `User` and account for it in `UserTest`.

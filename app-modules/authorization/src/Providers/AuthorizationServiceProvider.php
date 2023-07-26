<?php

namespace Assist\Authorization\Providers;

use Assist\Authorization\Models\Role;
use Illuminate\Support\ServiceProvider;
use Assist\Authorization\Models\RoleGroup;
use Assist\Authorization\Models\Permission;
use Illuminate\Database\Eloquent\Relations\Relation;

class AuthorizationServiceProvider extends ServiceProvider
{
    public function register()
    {
    }

    public function boot()
    {
        Relation::morphMap([
            'role' => Role::class,
            'permission' => Permission::class,
            'roleGroup' => RoleGroup::class,
        ]);
    }
}

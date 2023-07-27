<?php

namespace Assist\Authorization\Providers;

use Filament\Panel;
use Assist\Authorization\Models\Role;
use Illuminate\Support\ServiceProvider;
use Assist\Authorization\Models\RoleGroup;
use Assist\Authorization\Models\Permission;
use Assist\Authorization\AuthorizationPlugin;
use Illuminate\Database\Eloquent\Relations\Relation;

class AuthorizationServiceProvider extends ServiceProvider
{
    public function register()
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new AuthorizationPlugin()));
    }

    public function boot()
    {
        Relation::morphMap([
            'role' => Role::class,
            'permission' => Permission::class,
            'role_group' => RoleGroup::class,
        ]);
    }
}

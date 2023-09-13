<?php

namespace Assist\Theme\Providers;

use Assist\Audit\Models\Audit;
use Assist\Authorization\AuthorizationPermissionRegistry;
use Assist\Authorization\AuthorizationRoleRegistry;
use Filament\Panel;
use Assist\Theme\ThemePlugin;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new ThemePlugin()));
    }

    public function boot(AuthorizationPermissionRegistry $permissionRegistry, AuthorizationRoleRegistry $roleRegistry): void
    {
        Relation::morphMap(
            [],
        );
    }
}

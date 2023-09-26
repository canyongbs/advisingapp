<?php

namespace Assist\Form\Providers;

use Filament\Panel;
use Assist\Form\FormPlugin;
use Assist\Form\Models\Form;
use Assist\Form\Models\FormItem;
use Illuminate\Support\ServiceProvider;
use Assist\Authorization\AuthorizationRoleRegistry;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Authorization\AuthorizationPermissionRegistry;

class FormServiceProvider extends ServiceProvider
{
    public function register()
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new FormPlugin()));
    }

    public function boot()
    {
        Relation::morphMap([
            'form' => Form::class,
            'form_item' => FormItem::class,
        ]);

        $this->registerRolesAndPermissions();
    }

    protected function registerRolesAndPermissions()
    {
        $permissionRegistry = app(AuthorizationPermissionRegistry::class);

        $permissionRegistry->registerApiPermissions(
            module: 'form',
            path: 'permissions/api/custom'
        );

        $permissionRegistry->registerWebPermissions(
            module: 'form',
            path: 'permissions/web/custom'
        );

        $roleRegistry = app(AuthorizationRoleRegistry::class);

        $roleRegistry->registerApiRoles(
            module: 'form',
            path: 'roles/api'
        );

        $roleRegistry->registerWebRoles(
            module: 'form',
            path: 'roles/web'
        );
    }
}

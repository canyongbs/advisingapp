<?php

namespace Assist\Form\Providers;

use Filament\Panel;
use Assist\Form\FormPlugin;
use Assist\Form\Models\Form;
use Assist\Form\Models\FormField;
use Assist\Form\Models\FormSubmission;
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
            'form_field' => FormField::class,
            'form_submission' => FormSubmission::class,
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

<?php

namespace Assist\CaseloadManagement\Providers;

use Assist\CaseloadManagement\Models\CaseloadSubject;
use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use Assist\CaseloadManagement\Models\Caseload;
use Assist\Authorization\AuthorizationRoleRegistry;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\CaseloadManagement\CaseloadManagementPlugin;
use Assist\Authorization\AuthorizationPermissionRegistry;

class CaseloadManagementServiceProvider extends ServiceProvider
{
    public function register()
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new CaseloadManagementPlugin()));
    }

    public function boot()
    {
        Relation::morphMap([
            'caseload' => Caseload::class,
            'caseload_subject' => CaseloadSubject::class
        ]);

        $this->registerRolesAndPermissions();
    }

    protected function registerRolesAndPermissions()
    {
        $permissionRegistry = app(AuthorizationPermissionRegistry::class);

        $permissionRegistry->registerApiPermissions(
            module: 'caseload-management',
            path: 'permissions/api/custom'
        );

        $permissionRegistry->registerWebPermissions(
            module: 'caseload-management',
            path: 'permissions/web/custom'
        );

        $roleRegistry = app(AuthorizationRoleRegistry::class);

        $roleRegistry->registerApiRoles(
            module: 'caseload-management',
            path: 'roles/api'
        );

        $roleRegistry->registerWebRoles(
            module: 'caseload-management',
            path: 'roles/web'
        );
    }
}

<?php

namespace Assist\Audit\Providers;

use Illuminate\Support\ServiceProvider;
use Assist\Authorization\AuthorizationRoleRegistry;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Authorization\AuthorizationPermissionRegistry;

class AuditServiceProvider extends ServiceProvider
{
    public function register()
    {
        app('config')->set('audit', require base_path('app-modules/audit/config/audit.php'));
        app('config')->set('settings', require base_path('app-modules/audit/config/settings.php'));
    }

    public function boot(AuthorizationPermissionRegistry $permissionRegistry, AuthorizationRoleRegistry $roleRegistry): void
    {
        Relation::morphMap([]);

        $permissionRegistry->registerApiPermissions(
            module: 'audit',
            path: 'permissions/api/custom'
        );

        $permissionRegistry->registerWebPermissions(
            module: 'audit',
            path: 'permissions/web/custom'
        );

        $roleRegistry->registerApiRoles(
            module: 'audit',
            path: 'roles/api'
        );

        $roleRegistry->registerWebRoles(
            module: 'audit',
            path: 'roles/web'
        );
    }
}

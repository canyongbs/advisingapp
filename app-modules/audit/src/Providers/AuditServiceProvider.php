<?php

namespace Assist\Audit\Providers;

use Filament\Panel;
use Assist\Audit\AuditPlugin;
use Assist\Audit\Models\Audit;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Assist\Authorization\AuthorizationRoleRegistry;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Authorization\AuthorizationPermissionRegistry;

class AuditServiceProvider extends ServiceProvider
{
    public function register()
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new AuditPlugin()));

        app('config')->set('audit', require base_path('app-modules/audit/config/audit.php'));
        app('config')->set('settings', require base_path('app-modules/audit/config/settings.php'));
    }

    public function boot(AuthorizationPermissionRegistry $permissionRegistry, AuthorizationRoleRegistry $roleRegistry): void
    {
        Relation::morphMap(
            [
                'audit' => Audit::class,
            ]
        );

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

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->command('audit:purge-past-retention-audit-records')
                ->daily()
                ->evenInMaintenanceMode();
        });
    }
}

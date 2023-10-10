<?php

namespace Assist\AssistDataModel\Providers;

use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use Assist\AssistDataModel\Models\Program;
use Assist\AssistDataModel\Models\Student;
use Assist\AssistDataModel\Models\Enrollment;
use Assist\AssistDataModel\Models\Performance;
use Assist\AssistDataModel\AssistDataModelPlugin;
use Assist\Authorization\AuthorizationRoleRegistry;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Authorization\AuthorizationPermissionRegistry;

class AssistDataModelServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new AssistDataModelPlugin()));
    }

    public function boot(): void
    {
        Relation::morphMap([
            'student' => Student::class,
            'enrollment' => Enrollment::class,
            'performance' => Performance::class,
            'program' => Program::class,
        ]);

        $this->registerRolesAndPermissions();
    }

    protected function registerRolesAndPermissions(): void
    {
        $permissionRegistry = app(AuthorizationPermissionRegistry::class);

        $permissionRegistry->registerApiPermissions(
            module: 'assist-data-model',
            path: 'permissions/api/custom'
        );

        $permissionRegistry->registerWebPermissions(
            module: 'assist-data-model',
            path: 'permissions/web/custom'
        );

        $roleRegistry = app(AuthorizationRoleRegistry::class);

        $roleRegistry->registerApiRoles(
            module: 'assist-data-model',
            path: 'roles/api'
        );

        $roleRegistry->registerWebRoles(
            module: 'assist-data-model',
            path: 'roles/web'
        );
    }
}

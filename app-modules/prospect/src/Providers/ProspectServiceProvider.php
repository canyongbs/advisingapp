<?php

namespace Assist\Prospect\Providers;

use Assist\Prospect\Models\Prospect;
use Illuminate\Support\ServiceProvider;
use Assist\Prospect\Models\ProspectSource;
use Assist\Prospect\Models\ProspectStatus;
use Assist\Authorization\AuthorizationRoleRegistry;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Authorization\AuthorizationPermissionRegistry;

class ProspectServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(AuthorizationPermissionRegistry $permissionRegistry, AuthorizationRoleRegistry $roleRegistry): void
    {
        Relation::morphMap([
            'prospect' => Prospect::class,
            'prospect_source' => ProspectSource::class,
            'prospect_status' => ProspectStatus::class,
        ]);

        $permissionRegistry->registerApiPermissions(
            module: 'prospect',
            path: 'permissions/api/custom'
        );

        $permissionRegistry->registerWebPermissions(
            module: 'prospect',
            path: 'permissions/web/custom'
        );

        $roleRegistry->registerApiRoles(
            module: 'prospect',
            path: 'roles/api'
        );

        $roleRegistry->registerWebRoles(
            module: 'prospect',
            path: 'roles/web'
        );
    }
}

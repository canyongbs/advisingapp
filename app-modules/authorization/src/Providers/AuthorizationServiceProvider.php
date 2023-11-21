<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Authorization\Providers;

use Filament\Panel;
use Assist\Authorization\Models\Role;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Assist\Authorization\Models\RoleGroup;
use Assist\Authorization\Models\Permission;
use Assist\Authorization\AuthorizationPlugin;
use SocialiteProviders\Azure\AzureExtendSocialite;
use SocialiteProviders\Manager\SocialiteWasCalled;
use Assist\Authorization\AuthorizationRoleRegistry;
use Illuminate\Database\Eloquent\Relations\Relation;
use SocialiteProviders\Google\GoogleExtendSocialite;
use Assist\Authorization\AuthorizationPermissionRegistry;

class AuthorizationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new AuthorizationPlugin()));

        $this->app->singleton(AuthorizationPermissionRegistry::class, function ($app) {
            return new AuthorizationPermissionRegistry();
        });

        $this->app->singleton(AuthorizationRoleRegistry::class, function ($app) {
            return new AuthorizationRoleRegistry();
        });

        app('config')->set('permission', require base_path('app-modules/authorization/config/permission.php'));
    }

    public function boot(AuthorizationPermissionRegistry $permissionRegistry, AuthorizationRoleRegistry $roleRegistry): void
    {
        Relation::morphMap([
            'role' => Role::class,
            'permission' => Permission::class,
            'role_group' => RoleGroup::class,
        ]);

        $permissionRegistry->registerApiPermissions(
            module: 'authorization',
            path: 'permissions/api/custom'
        );

        $permissionRegistry->registerWebPermissions(
            module: 'authorization',
            path: 'permissions/web/custom'
        );

        $roleRegistry->registerApiRoles(
            module: 'authorization',
            path: 'roles/api'
        );

        $roleRegistry->registerWebRoles(
            module: 'authorization',
            path: 'roles/web'
        );

        Event::listen(
            events: SocialiteWasCalled::class,
            listener: AzureExtendSocialite::class . '@handle'
        );

        Event::listen(
            events: SocialiteWasCalled::class,
            listener: GoogleExtendSocialite::class . '@handle'
        );
    }
}

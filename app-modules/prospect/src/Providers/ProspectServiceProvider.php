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

namespace Assist\Prospect\Providers;

use Filament\Panel;
use Assist\Prospect\ProspectPlugin;
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
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new ProspectPlugin()));
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

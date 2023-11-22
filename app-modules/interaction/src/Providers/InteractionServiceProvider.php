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

namespace Assist\Interaction\Providers;

use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use Assist\Interaction\InteractionPlugin;
use Assist\Interaction\Models\Interaction;
use Assist\Interaction\Models\InteractionType;
use Assist\Interaction\Models\InteractionDriver;
use Assist\Interaction\Models\InteractionStatus;
use Assist\Interaction\Models\InteractionOutcome;
use Assist\Interaction\Models\InteractionCampaign;
use Assist\Interaction\Models\InteractionRelation;
use Assist\Authorization\AuthorizationRoleRegistry;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Interaction\Observers\InteractionObserver;
use Assist\Authorization\AuthorizationPermissionRegistry;

class InteractionServiceProvider extends ServiceProvider
{
    public function register()
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new InteractionPlugin()));
    }

    public function boot()
    {
        Relation::morphMap([
            'interaction' => Interaction::class,
            'interaction_campaign' => InteractionCampaign::class,
            'interaction_driver' => InteractionDriver::class,
            'interaction_outcome' => InteractionOutcome::class,
            'interaction_relation' => InteractionRelation::class,
            'interaction_status' => InteractionStatus::class,
            'interaction_type' => InteractionType::class,
        ]);

        $this->registerRolesAndPermissions();
        $this->registerObservers();
    }

    protected function registerRolesAndPermissions()
    {
        $permissionRegistry = app(AuthorizationPermissionRegistry::class);

        $permissionRegistry->registerApiPermissions(
            module: 'interaction',
            path: 'permissions/api/custom'
        );

        $permissionRegistry->registerWebPermissions(
            module: 'interaction',
            path: 'permissions/web/custom'
        );

        $roleRegistry = app(AuthorizationRoleRegistry::class);

        $roleRegistry->registerApiRoles(
            module: 'interaction',
            path: 'roles/api'
        );

        $roleRegistry->registerWebRoles(
            module: 'interaction',
            path: 'roles/web'
        );
    }

    protected function registerObservers(): void
    {
        Interaction::observe(InteractionObserver::class);
    }
}

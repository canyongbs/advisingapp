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

namespace Assist\Engagement\Providers;

use Filament\Panel;
use Assist\Engagement\EngagementPlugin;
use Illuminate\Support\ServiceProvider;
use Assist\Engagement\Models\Engagement;
use Assist\Engagement\Models\EmailTemplate;
use Illuminate\Console\Scheduling\Schedule;
use Assist\Engagement\Models\EngagementFile;
use Assist\Engagement\Models\EngagementBatch;
use Assist\Engagement\Models\EngagementResponse;
use Assist\Engagement\Actions\DeliverEngagements;
use Assist\Authorization\AuthorizationRoleRegistry;
use Assist\Engagement\Models\EngagementDeliverable;
use Assist\Engagement\Observers\EngagementObserver;
use Assist\Engagement\Models\EngagementFileEntities;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Engagement\Observers\EngagementBatchObserver;
use Assist\Authorization\AuthorizationPermissionRegistry;
use Assist\Engagement\Observers\EngagementFileEntitiesObserver;

class EngagementServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new EngagementPlugin()));
    }

    public function boot(): void
    {
        Relation::morphMap([
            'engagement' => Engagement::class,
            'engagement_deliverable' => EngagementDeliverable::class,
            'engagement_batch' => EngagementBatch::class,
            'engagement_response' => EngagementResponse::class,
            'engagement_file' => EngagementFile::class,
            'email_template' => EmailTemplate::class,
        ]);

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            // TODO Ensure we are locking entities that have already been picked up for processing to avoid overlap
            $schedule->job(DeliverEngagements::class)
                ->everyMinute()
                ->withoutOverlapping();
        });

        $this->registerRolesAndPermissions();

        $this->registerObservers();
    }

    public function registerObservers(): void
    {
        EngagementFileEntities::observe(EngagementFileEntitiesObserver::class);
        Engagement::observe(EngagementObserver::class);
        EngagementBatch::observe(EngagementBatchObserver::class);
    }

    protected function registerRolesAndPermissions()
    {
        $permissionRegistry = app(AuthorizationPermissionRegistry::class);

        $permissionRegistry->registerApiPermissions(
            module: 'engagement',
            path: 'permissions/api/custom'
        );

        $permissionRegistry->registerWebPermissions(
            module: 'engagement',
            path: 'permissions/web/custom'
        );

        $roleRegistry = app(AuthorizationRoleRegistry::class);

        $roleRegistry->registerApiRoles(
            module: 'engagement',
            path: 'roles/api'
        );

        $roleRegistry->registerWebRoles(
            module: 'engagement',
            path: 'roles/web'
        );
    }
}

<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Engagement\Providers;

use Assist\Engagement\Observers\EmailTemplateObserver;
use Filament\Panel;
use Assist\Engagement\EngagementPlugin;
use Illuminate\Support\ServiceProvider;
use Assist\Engagement\Models\Engagement;
use Assist\Engagement\Models\SmsTemplate;
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
            'sms_template' => SmsTemplate::class,
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
        EmailTemplate::observe(EmailTemplateObserver::class);
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

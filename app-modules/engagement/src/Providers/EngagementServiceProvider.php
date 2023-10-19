<?php

namespace Assist\Engagement\Providers;

use Filament\Panel;
use Assist\Engagement\EngagementPlugin;
use Illuminate\Support\ServiceProvider;
use Assist\Engagement\Models\Engagement;
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

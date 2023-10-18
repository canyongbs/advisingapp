<?php

namespace Assist\Campaign\Providers;

use Filament\Panel;
use Assist\Campaign\CampaignPlugin;
use Assist\Campaign\Models\Campaign;
use Illuminate\Support\ServiceProvider;
use Assist\Campaign\Models\CampaignAction;
use Illuminate\Console\Scheduling\Schedule;
use Assist\Campaign\Actions\ExecuteCampaigns;
use Assist\Campaign\Observers\CampaignObserver;
use Assist\Authorization\AuthorizationRoleRegistry;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Authorization\AuthorizationPermissionRegistry;

class CampaignServiceProvider extends ServiceProvider
{
    public function register()
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new CampaignPlugin()));
    }

    public function boot()
    {
        Relation::morphMap([
            'campaign' => Campaign::class,
            'campaign_action' => CampaignAction::class,
        ]);

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            // TODO Ensure we are locking entities that have already been picked up for processing to avoid overlap
            $schedule->job(ExecuteCampaigns::class)
                ->everyMinute()
                ->withoutOverlapping();
        });

        $this->registerRolesAndPermissions();

        $this->registerObservers();
    }

    public function registerObservers(): void
    {
        Campaign::observe(CampaignObserver::class);
    }

    protected function registerRolesAndPermissions()
    {
        $permissionRegistry = app(AuthorizationPermissionRegistry::class);

        $permissionRegistry->registerApiPermissions(
            module: 'campaign',
            path: 'permissions/api/custom'
        );

        $permissionRegistry->registerWebPermissions(
            module: 'campaign',
            path: 'permissions/web/custom'
        );

        $roleRegistry = app(AuthorizationRoleRegistry::class);

        $roleRegistry->registerApiRoles(
            module: 'campaign',
            path: 'roles/api'
        );

        $roleRegistry->registerWebRoles(
            module: 'campaign',
            path: 'roles/web'
        );
    }
}

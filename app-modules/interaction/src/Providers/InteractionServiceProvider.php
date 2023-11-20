<?php

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

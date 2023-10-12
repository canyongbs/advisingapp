<?php

namespace Assist\Notifications\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Assist\Notifications\Models\Subscription;
use Assist\Authorization\AuthorizationRoleRegistry;
use Assist\Notifications\Events\SubscriptionCreated;
use Assist\Notifications\Events\SubscriptionDeleted;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Notifications\Observers\SubscriptionObserver;
use Assist\Authorization\AuthorizationPermissionRegistry;
use Assist\Notifications\Events\TriggeredAutoSubscription;
use Assist\Notifications\Listeners\CreateAutoSubscription;
use Assist\Notifications\Listeners\NotifyUserOfSubscriptionCreated;
use Assist\Notifications\Listeners\NotifyUserOfSubscriptionDeleted;

class NotificationsServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Relation::morphMap([
            'subscription' => Subscription::class,
        ]);

        $this->registerRolesAndPermissions();
        $this->registerObservers();
        $this->registerevents();
    }

    protected function registerObservers(): void
    {
        Subscription::observe(SubscriptionObserver::class);
    }

    protected function registerEvents(): void
    {
        Event::listen(
            SubscriptionCreated::class,
            NotifyUserOfSubscriptionCreated::class
        );

        Event::listen(
            SubscriptionDeleted::class,
            NotifyUserOfSubscriptionDeleted::class
        );

        Event::listen(
            TriggeredAutoSubscription::class,
            CreateAutoSubscription::class,
        );
    }

    protected function registerRolesAndPermissions(): void
    {
        $permissionRegistry = app(AuthorizationPermissionRegistry::class);

        $permissionRegistry->registerApiPermissions(
            module: 'notifications',
            path: 'permissions/api/custom'
        );

        $permissionRegistry->registerWebPermissions(
            module: 'notifications',
            path: 'permissions/web/custom'
        );

        $roleRegistry = app(AuthorizationRoleRegistry::class);

        $roleRegistry->registerApiRoles(
            module: 'notifications',
            path: 'roles/api'
        );

        $roleRegistry->registerWebRoles(
            module: 'notifications',
            path: 'roles/web'
        );
    }
}

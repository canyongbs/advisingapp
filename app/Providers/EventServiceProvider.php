<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Assist\Authorization\Events\RoleRemovedFromUser;
use Assist\Authorization\Events\RoleGroupRolePivotSaved;
use Assist\Authorization\Events\RoleGroupUserPivotSaved;
use Assist\Authorization\Events\RoleGroupRolePivotDeleted;
use Assist\Authorization\Events\RoleGroupUserPivotDeleted;
use Assist\Authorization\Listeners\HandleRoleRemovedFromUser;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Assist\Authorization\Listeners\HandleRoleGroupRolePivotSaved;
use Assist\Authorization\Listeners\HandleRoleGroupUserPivotSaved;
use Assist\Authorization\Listeners\HandleRoleGroupRolePivotDeleted;
use Assist\Authorization\Listeners\HandleRoleGroupUserPivotDeleted;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        // TODO Extract these into the authorization module
        RoleGroupUserPivotSaved::class => [
            HandleRoleGroupUserPivotSaved::class,
        ],
        RoleGroupRolePivotSaved::class => [
            HandleRoleGroupRolePivotSaved::class,
        ],
        RoleGroupUserPivotDeleted::class => [
            HandleRoleGroupUserPivotDeleted::class,
        ],
        RoleGroupRolePivotDeleted::class => [
            HandleRoleGroupRolePivotDeleted::class,
        ],
        RoleRemovedFromUser::class => [
            HandleRoleRemovedFromUser::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Assist\Authorization\Events\RoleGroupPivotSaved;
use Assist\Authorization\Events\RoleGroupPivotDeleted;
use Assist\Authorization\Listeners\HandleRoleGroupPivotSaved;
use Assist\Authorization\Listeners\HandleRoleGroupPivotDeleted;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
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
        RoleGroupPivotSaved::class => [
            HandleRoleGroupPivotSaved::class,
        ],
        RoleGroupPivotDeleted::class => [
            HandleRoleGroupPivotDeleted::class,
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

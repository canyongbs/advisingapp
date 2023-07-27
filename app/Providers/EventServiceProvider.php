<?php

namespace App\Providers;

use App\Events\RoleGroupPivotSaved;
use App\Events\RoleGroupPivotDeleted;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use App\Listeners\HandleRoleGroupPivotSaved;
use App\Listeners\HandleRoleGroupPivotDeleted;
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

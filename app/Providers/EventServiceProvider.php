<?php

namespace App\Providers;

use OwenIt\Auditing\Events\Auditing;
use Illuminate\Auth\Events\Registered;
use Assist\Audit\Listeners\AuditingListener;
use Assist\Authorization\Events\RoleRemovedFromUser;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Notifications\Events\NotificationFailed;
use Assist\Authorization\Events\RoleAttachedToRoleGroup;
use Assist\Authorization\Events\UserAttachedToRoleGroup;
use Assist\Authorization\Events\RoleRemovedFromRoleGroup;
use Assist\Authorization\Events\UserRemovedFromRoleGroup;
use Assist\Authorization\Listeners\HandleRoleRemovedFromUser;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Assist\Authorization\Listeners\HandleRoleAttachedToRoleGroup;
use Assist\Authorization\Listeners\HandleUserAttachedToRoleGroup;
use Assist\Engagement\Listeners\HandleEngagementNotificationSent;
use Assist\Authorization\Listeners\HandleRoleRemovedFromRoleGroup;
use Assist\Authorization\Listeners\HandleUserRemovedFromRoleGroup;
use Assist\Engagement\Listeners\HandleEngagementNotificationFailed;
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
        UserAttachedToRoleGroup::class => [
            HandleUserAttachedToRoleGroup::class,
        ],
        RoleAttachedToRoleGroup::class => [
            HandleRoleAttachedToRoleGroup::class,
        ],
        UserRemovedFromRoleGroup::class => [
            HandleUserRemovedFromRoleGroup::class,
        ],
        RoleRemovedFromRoleGroup::class => [
            HandleRoleRemovedFromRoleGroup::class,
        ],
        RoleRemovedFromUser::class => [
            HandleRoleRemovedFromUser::class,
        ],
        // TODO: Move this to the auditing Module somehow
        Auditing::class => [
            AuditingListener::class,
        ],
        // Move this to the appropriate module - currently being used with Engagement
        NotificationSent::class => [
            HandleEngagementNotificationSent::class,
        ],
        NotificationFailed::class => [
            HandleEngagementNotificationFailed::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void {}

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}

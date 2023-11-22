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
        // TODO Introduce generic handler here that only then dispatches the appropriate listener per the notification
        NotificationSent::class => [
            HandleEngagementNotificationSent::class,
        ],
        // TODO Introduce generic handler here that only then dispatches the appropriate listener per the notification
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

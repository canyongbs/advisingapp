<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace App\Providers;

use AdvisingApp\Audit\Listeners\AuditingListener;
use AdvisingApp\Report\Listeners\ProcessUserUniqueLoginTrackedEvent;
use App\Listeners\ClearSentryUser;
use App\Listeners\LoadSettingsDefaults;
use App\Listeners\SetSentryUser;
use App\Listeners\SyncScheduleMonitor;
use App\Multitenancy\Events\NewTenantSetupComplete;
use App\Multitenancy\Listeners\RemoveSentryTenantTag;
use App\Multitenancy\Listeners\SetSentryTenantTag;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Console\Events\ScheduledTaskStarting;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use OwenIt\Auditing\Events\Auditing;
use Spatie\LaravelSettings\Events\LoadingSettings;
use Spatie\Multitenancy\Events\ForgotCurrentTenantEvent;
use Spatie\Multitenancy\Events\MakingTenantCurrentEvent;

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
        Auditing::class => [
            AuditingListener::class,
        ],
        MakingTenantCurrentEvent::class => [
            SetSentryTenantTag::class,
        ],
        ForgotCurrentTenantEvent::class => [
            RemoveSentryTenantTag::class,
        ],
        Login::class => [
            SetSentryUser::class,
            ProcessUserUniqueLoginTrackedEvent::class,
        ],
        Authenticated::class => [
            SetSentryUser::class,
        ],
        ScheduledTaskStarting::class => [
            ClearSentryUser::class,
        ],
        Logout::class => [
            ClearSentryUser::class,
        ],
        LoadingSettings::class => [
            LoadSettingsDefaults::class,
        ],
        NewTenantSetupComplete::class => [
            SyncScheduleMonitor::class,
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

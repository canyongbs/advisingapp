<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\IntegrationTwilio\Providers;

use Filament\Panel;
use App\Models\Tenant;
use Twilio\Rest\Client;
use App\Enums\Integration;
use App\Models\Scopes\SetupIsComplete;
use Illuminate\Support\ServiceProvider;
use App\Exceptions\IntegrationException;
use Illuminate\Console\Scheduling\Schedule;
use AdvisingApp\IntegrationTwilio\IntegrationTwilioPlugin;
use AdvisingApp\IntegrationTwilio\Settings\TwilioSettings;
use AdvisingApp\Engagement\Actions\FindEngagementResponseSender;
use AdvisingApp\Engagement\Actions\Contracts\EngagementResponseSenderFinder;
use AdvisingApp\IntegrationTwilio\Jobs\CheckStatusOfOutboundDeliverablesWithoutATerminalStatus;
use AdvisingApp\IntegrationTwilio\Actions\Playground\FindEngagementResponseSender as PlaygroundFindEngagementResponseSender;

class IntegrationTwilioServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Panel::configureUsing(fn (Panel $panel) => ($panel->getId() !== 'admin') || $panel->plugin(new IntegrationTwilioPlugin()));

        $this->app->scoped(EngagementResponseSenderFinder::class, function () {
            if (config('local_development.twilio.enable_test_sender') === true) {
                return new PlaygroundFindEngagementResponseSender();
            }

            return new FindEngagementResponseSender();
        });

        $settings = $this->app->make(TwilioSettings::class);

        $this->app->scoped(
            Client::class,
            fn () => Integration::Twilio->isOn()
                ? new Client($settings->account_sid, $settings->auth_token)
                : throw IntegrationException::make(Integration::Twilio)
        );

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->call(function () {
                Tenant::query()
                    ->tap(new SetupIsComplete())
                    ->cursor()
                    ->each(function (Tenant $tenant) {
                        $tenant->execute(function () {
                            dispatch(new CheckStatusOfOutboundDeliverablesWithoutATerminalStatus());
                        });
                    });
            })
                ->daily()
                ->name('CheckStatusOfOutboundDeliverablesWithoutATerminalStatus')
                ->onOneServer()
                ->withoutOverlapping();
        });
    }

    public function boot(): void {}
}

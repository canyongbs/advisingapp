<?php

namespace Assist\IntegrationTwilio\Providers;

use Twilio\Rest\Client;
use Illuminate\Support\ServiceProvider;
use Assist\Engagement\Actions\FindEngagementResponseSender;
use Assist\Engagement\Actions\Contracts\EngagementResponseSenderFinder;
use Assist\IntegrationTwilio\Actions\Playground\FindEngagementResponseSender as PlaygroundFindEngagementResponseSender;

class IntegrationTwilioServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(EngagementResponseSenderFinder::class, function ($app) {
            if (config('services.twilio.enable_test_sender') === true) {
                return new PlaygroundFindEngagementResponseSender();
            }

            return new FindEngagementResponseSender();
        });

        $this->app->bind(Client::class, fn () => new Client(
            config('services.twilio.account_sid'),
            config('services.twilio.auth_token')
        ));
    }

    public function boot(): void {}
}

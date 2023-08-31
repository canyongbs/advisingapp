<?php

namespace Assist\IntegrationTwilio\Providers;

use Illuminate\Support\ServiceProvider;
use Assist\Engagement\Actions\FindEngagementResponseSender;
use Assist\Engagement\Actions\Contracts\EngagementResponseSenderFinder;
use Assist\IntegrationTwilio\Actions\Playground\FindEngagementResponseSender as PlaygroundFindEngagementResponseSender;

class IntegrationTwilioServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(EngagementResponseSenderFinder::class, function ($app) {
            if (config('services.twilio.enable_test_sender') === true) {
                return new PlaygroundFindEngagementResponseSender();
            }

            return new FindEngagementResponseSender();
        });
    }

    public function boot() {}
}

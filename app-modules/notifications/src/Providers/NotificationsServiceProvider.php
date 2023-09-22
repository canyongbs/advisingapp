<?php

namespace Assist\Notifications\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Assist\Notifications\Models\Subscription;
use Assist\Notifications\Events\SubscriptionCreated;
use Assist\Notifications\Events\SubscriptionDeleted;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Notifications\Observers\SubscriptionObserver;
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

        $this->observers();

        $this->events();
    }

    protected function observers(): void
    {
        Subscription::observe(SubscriptionObserver::class);
    }

    protected function events(): void
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
}

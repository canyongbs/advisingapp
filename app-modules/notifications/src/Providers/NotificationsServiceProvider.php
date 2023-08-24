<?php

namespace Assist\Notifications\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Assist\Notifications\Models\Subscription;
use Assist\Notifications\Events\SubscriptionCreated;
use Assist\Notifications\Events\SubscriptionDeleted;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Notifications\Observers\SubscriptionObserver;
use Assist\Notifications\Listeners\NotifyUserOfSubscriptionCreated;
use Assist\Notifications\Listeners\NotifyUserOfSubscriptionDeleted;

class NotificationsServiceProvider extends ServiceProvider
{
    public function register() {}

    public function boot()
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
    }
}

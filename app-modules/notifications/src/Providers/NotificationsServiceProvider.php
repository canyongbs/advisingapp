<?php

namespace Assist\Notifications\Providers;

use Illuminate\Support\ServiceProvider;
use Assist\Notifications\Models\Subscription;
use Illuminate\Database\Eloquent\Relations\Relation;

class NotificationsServiceProvider extends ServiceProvider
{
    public function register()
    {
    }

    public function boot()
    {
        Relation::morphMap([
            'subscription' => Subscription::class,
        ]);
    }
}

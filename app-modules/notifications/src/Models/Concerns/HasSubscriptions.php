<?php

namespace Assist\Notifications\Models\Concerns;

use Assist\Notifications\Models\Subscription;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasSubscriptions
{
    public function subscriptions(): MorphMany
    {
        return $this->morphMany(Subscription::class, 'subscribable');
    }

    public function getSubscriptionDisplayName(): string
    {
        return $this->full_name;
    }
}

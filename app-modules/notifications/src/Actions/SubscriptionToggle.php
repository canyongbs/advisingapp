<?php

namespace Assist\Notifications\Actions;

use App\Models\User;
use Assist\Notifications\Models\Contracts\Subscribable;

class SubscriptionToggle
{
    public function handle(User $user, Subscribable $subscribable)
    {
        $subscription = $user->subscriptions()->where([
            'subscribable_id' => $subscribable->getKey(),
            'subscribable_type' => $subscribable->getMorphClass(),
        ])->first();

        if ($subscription) {
            $subscription->delete();
        } else {
            resolve(SubscriptionCreate::class)->handle($user, $subscribable);
        }
    }
}

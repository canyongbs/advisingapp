<?php

namespace Assist\Interaction\Observers;

use Assist\Interaction\Models\Interaction;
use Assist\Notifications\Events\TriggeredAutoSubscription;

class InteractionObserver
{
    public function creating(Interaction $interaction): void
    {
        if (is_null($interaction->user_id) && ! is_null(auth()->user())) {
            $interaction->user_id = auth()->user()->id;
        }

        if (is_null($interaction->start_datetime)) {
            $interaction->start_datetime = now();
        }
    }

    public function created(Interaction $interaction): void
    {
        if ($user = auth()->user()) {
            TriggeredAutoSubscription::dispatch($user, $interaction);
        }
    }
}

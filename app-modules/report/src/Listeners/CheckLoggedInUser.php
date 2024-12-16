<?php

namespace AdvisingApp\Report\Listeners;

use AdvisingApp\Report\Enums\TrackedEventType;
use AdvisingApp\Report\Jobs\RecordUserTrackedEvent;
use App\Models\Authenticatable;
use App\Models\User;
use Illuminate\Auth\Events\Login;

class CheckLoggedInUser
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;

        if ($user instanceof User) {
            \Log::debug('listener');
            dispatch(new RecordUserTrackedEvent(
                type: TrackedEventType::UserLogin,
                occurredAt: now(),
                user: $user,
            ));
        }
    }
}

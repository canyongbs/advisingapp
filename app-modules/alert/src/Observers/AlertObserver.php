<?php

namespace Assist\Alert\Observers;

use Assist\Alert\Models\Alert;
use Assist\Alert\Events\AlertCreated;
use Assist\Notifications\Events\TriggeredAutoSubscription;

class AlertObserver
{
    public function created(Alert $alert): void
    {
        if ($user = auth()->user()) {
            TriggeredAutoSubscription::dispatch($user, $alert);
        }

        AlertCreated::dispatch($alert);
    }
}

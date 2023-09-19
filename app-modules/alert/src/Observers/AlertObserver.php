<?php

namespace Assist\Alert\Observers;

use Assist\Alert\Models\Alert;
use Assist\Alert\Events\AlertCreated;
use Illuminate\Support\Facades\Cache;
use Assist\Notifications\Actions\SubscriptionCreate;

class AlertObserver
{
    public function created(Alert $alert): void
    {
        if ($user = auth()->user()) {
            // Creating the subscription directly so that the alert can be sent to this User as well
            resolve(SubscriptionCreate::class)->handle($user, $alert->getSubscribable(), false);
        }

        AlertCreated::dispatch($alert);
    }

    public function saved(Alert $alert): void
    {
        Cache::tags('alert-count')->flush();
    }

    public function deleted(Alert $alert): void
    {
        Cache::tags('alert-count')->flush();
    }
}

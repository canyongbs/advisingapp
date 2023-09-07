<?php

namespace Assist\Alert\Listeners;

use Assist\Prospect\Models\Prospect;
use Assist\Alert\Events\AlertCreated;
use Assist\AssistDataModel\Models\Student;
use Illuminate\Contracts\Queue\ShouldQueue;
use Assist\Notifications\Models\Subscription;
use Assist\Alert\Notifications\AlertCreatedNotification;

class NotifySubscribersOfAlertCreated implements ShouldQueue
{
    public function handle(AlertCreated $event): void
    {
        /** @var Student|Prospect $concern */
        $concern = $event->alert->concern;

        $concern->subscriptions->each(function (Subscription $subscription) use ($event) {
            $subscription->user->notify(new AlertCreatedNotification($event->alert));
        });
    }
}

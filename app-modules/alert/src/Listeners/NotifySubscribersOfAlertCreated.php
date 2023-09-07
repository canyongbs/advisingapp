<?php

namespace Assist\Alert\Listeners;

use Assist\Prospect\Models\Prospect;
use Assist\Alert\Events\AlertCreated;
use Assist\AssistDataModel\Models\Student;
use Illuminate\Contracts\Queue\ShouldQueue;
use Assist\Alert\Notifications\AlertCreatedNotification;

class NotifySubscribersOfAlertCreated implements ShouldQueue
{
    public function handle(AlertCreated $event): void
    {
        /** @var Student|Prospect $concern */
        $concern = $event->alert->concern;

        $concern->subscriptions->each(function ($subscriber) use ($event) {
            $subscriber->notify(new AlertCreatedNotification($event->alert));
        });
    }
}

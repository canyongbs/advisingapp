<?php

namespace Assist\Notifications\Listeners;

use Filament\Facades\Filament;
use Illuminate\Support\HtmlString;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Illuminate\Contracts\Queue\ShouldQueue;
use Assist\Notifications\Events\SubscriptionCreated;

class NotifyUserOfSubscriptionCreated implements ShouldQueue
{
    public function handle(SubscriptionCreated $event): void
    {
        $subscribable = $event->subscription->subscribable;

        $name = $subscribable->{$subscribable->displayNameKey()};

        $target = resolve(Filament::getModelResource($subscribable));

        $url = $target::getUrl('view', ['record' => $subscribable]);

        $link = new HtmlString("<a href='{$url}' target='_blank' class='underline'>{$name}</a>");

        $morph = str($subscribable->getMorphClass());

        Notification::make()
            ->success()
            ->title("You have been subscribed to {$morph} {$link}")
            ->sendToDatabase($event->subscription->user);
    }
}

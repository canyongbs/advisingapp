<?php

namespace Assist\Alert\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Assist\Alert\Models\Alert;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Filament\Notifications\Notification as FilamentNotification;

class AlertCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Alert $alert) {}

    public function via(User $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(User $notifiable): array
    {
        return FilamentNotification::make()
            ->status('warning')
            ->title("A {$this->alert->severity->value} severity alert has been created for {$this->alert->getSubscribable()?->getSubscriptionDisplayName()}")
            ->toDatabase()
            ->data;
    }
}

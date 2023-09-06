<?php

namespace Assist\Engagement\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Filament\Notifications\Notification as FilamentNotification;

class EngagementBatchStartedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
    ) {}

    public function via(User $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject("We've started processing your bulk engagement, and we'll keep you updated on the progress.");
    }

    public function toDatabase(User $notifiable): array
    {
        return FilamentNotification::make()
            ->status('success')
            ->title('Bulk Engagement processing started')
            ->getDatabaseMessage();
    }
}

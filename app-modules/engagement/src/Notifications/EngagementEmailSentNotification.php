<?php

namespace Assist\Engagement\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Assist\Engagement\Models\Engagement;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Filament\Notifications\Notification as FilamentNotification;

class EngagementEmailSentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Engagement $engagement
    ) {}

    public function via(User $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject('Your Engagement Email has successfully been delivered.')
            // TODO Remove reliance on "preferred" as a field - extract to attribute
            ->line("Your engagement was successfully delivered to {$this->engagement->recipient->preferred}.");
    }

    public function toDatabase(User $notifiable): array
    {
        return FilamentNotification::make()
            ->success()
            ->title('Engagement Email Successfully Delivered')
            ->body("Your engagement email was successfully delivered to {$this->engagement->recipient->display_name}.")
            ->getDatabaseMessage();
    }
}

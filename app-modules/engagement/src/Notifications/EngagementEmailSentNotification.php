<?php

namespace Assist\Engagement\Notifications;

use App\Models\User;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use App\Notifications\MailMessage;
use Assist\Engagement\Models\Engagement;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
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
        return MailMessage::make()
            ->emailTemplate($this->resolveEmailTemplate())
            ->subject('Your Engagement Email has successfully been delivered.')
            ->line("Your engagement was successfully delivered to {$this->engagement->recipient->display_name}.");
    }

    public function toDatabase(User $notifiable): array
    {
        return FilamentNotification::make()
            ->success()
            ->title('Engagement Email Successfully Delivered')
            ->body("Your engagement email was successfully delivered to {$this->engagement->recipient->display_name}.")
            ->getDatabaseMessage();
    }

    private function resolveEmailTemplate(): ?EmailTemplate
    {
        return $this->engagement->createdBy->teams()->first()?->division?->emailTemplate;
    }
}

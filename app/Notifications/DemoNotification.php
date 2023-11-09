<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class DemoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected User $sender) {}

    /**
     * @return array<int, string>
     */
    public function via(User $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        return MailMessage::make()
            ->emailTemplate($this->resolveEmailTemplate($notifiable))
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(User $notifiable): array
    {
        return [];
    }

    private function resolveEmailTemplate(User $notifiable): ?EmailTemplate
    {
        return $this->sender->teams()->first()?->division?->emailTemplate;
    }
}

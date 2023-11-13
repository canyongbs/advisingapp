<?php

namespace App\Notifications;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\URL;
use Illuminate\Notifications\Notification;

class SetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return MailMessage::make()
            ->emailTemplate($this->resolveEmailTemplate())
            ->line('A new account has been created for you.')
            ->action('Set up your password', URL::temporarySignedRoute(
                'login.one-time',
                now()->addDay(),
                ['user' => $notifiable],
            ))
            ->line('For security reasons, this link will expire in 24 hours.')
            ->line('Please contact support if you need a new link or have any issues setting up your account.');
    }

    private function resolveEmailTemplate(): ?EmailTemplate
    {
        return null;
    }
}

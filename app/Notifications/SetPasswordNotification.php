<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\URL;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class SetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->line('A new account has been created for you.')
            ->action('Set up your password', URL::temporarySignedRoute(
                'login.one-time',
                now()->addDay(),
                ['user' => $notifiable],
            ))
            ->line('For security reasons, this link will expire in 24 hours.')
            ->line('Please contact support if you need a new link or have any issues setting up your account.');
    }
}

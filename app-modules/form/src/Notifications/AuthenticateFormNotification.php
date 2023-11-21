<?php

namespace Assist\Form\Notifications;

use Illuminate\Bus\Queueable;
use App\Notifications\MailMessage;
use Assist\Form\Models\FormAuthentication;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\AnonymousNotifiable;

class AuthenticateFormNotification extends Notification
{
    use Queueable;

    public function __construct(
        public FormAuthentication $formAuthentication,
        public int $code,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(AnonymousNotifiable $notifiable): MailMessage
    {
        return MailMessage::make()
            ->subject("Your authentication code for {$this->formAuthentication->form->name}")
            ->line("Your code is: {$this->code}.")
            ->line('You should type this code into the form to authenticate yourself.')
            ->line('For security reasons, the code will expire in 24 hours, but you can always request another.');
    }
}

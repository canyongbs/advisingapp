<?php

namespace Tests\Unit;

use AdvisingApp\Notification\Notifications\BaseNotification;
use AdvisingApp\Notification\Notifications\EmailNotification;
use AdvisingApp\Notification\Notifications\Messages\MailMessage;
use AdvisingApp\Notification\Notifications\Concerns\EmailChannelTrait;

class TestEmailNotification extends BaseNotification implements EmailNotification
{
    use EmailChannelTrait;

    public function toEmail(object $notifiable): MailMessage
    {
        return MailMessage::make()
            ->subject('Test Subject')
            ->greeting('Test Greeting')
            ->content('This is a test email')
            ->salutation('Test Salutation');
    }
}

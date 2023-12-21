<?php

namespace AdvisingApp\Notification\Notifications\Concerns;

use AdvisingApp\Notification\Notifications\Messages\MailMessage;
use AdvisingApp\Notification\Notifications\Channels\EmailChannel;

trait EmailChannelTrait
{
    use ChannelTrait;

    public static $channel = EmailChannel::class;

    public function toMail(object $notifiable): MailMessage
    {
        return $this->toEmail($notifiable)
            ->withSymfonyMessage(function ($message) {
                $message->metadata = $this->metadata;
            });
    }
}

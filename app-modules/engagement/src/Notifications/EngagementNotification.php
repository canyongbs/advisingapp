<?php

namespace Assist\Engagement\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Assist\Engagement\Models\EngagementDeliverable;

class EngagementNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected EngagementDeliverable $deliverable
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject($this->deliverable->engagement->subject)
            ->greeting('Hello ' . $this->deliverable->engagement->recipient->preferred . '!')
            ->line($this->deliverable->engagement->description)
            ->salutation("Regards, {$this->deliverable->engagement->user->name}");
    }
}

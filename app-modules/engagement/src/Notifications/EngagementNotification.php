<?php

namespace Assist\Engagement\Notifications;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use App\Notifications\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Assist\Engagement\Models\EngagementDeliverable;

class EngagementNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public EngagementDeliverable $deliverable
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return MailMessage::make()
            ->emailTemplate($this->resolveEmailTemplate())
            ->subject($this->deliverable->engagement->subject)
            ->greeting('Hello ' . $this->deliverable->engagement->recipient->display_name . '!')
            ->line($this->deliverable->engagement->body)
            ->salutation("Regards, {$this->deliverable->engagement->user->name}");
    }

    private function resolveEmailTemplate(): ?EmailTemplate
    {
        return $this->deliverable->engagement->createdBy->teams()->first()?->division?->emailTemplate;
    }
}

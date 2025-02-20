<?php

namespace AdvisingApp\CaseManagement\Notifications;

use AdvisingApp\CaseManagement\Models\CaseModel;
use AdvisingApp\Notification\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendClosedCaseFeedbackNotification extends Notification
{
    public function __construct(
        protected CaseModel $case,
    ) {}

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
            ->subject("Feedback survey for {$this->case->case_number}")
            ->greeting("Hi {$notifiable->display_name},")
            ->line('To help us serve you better in the future, we’d love to hear about your experience with our support team.')
            ->action('Rate Service', route('feedback.case', $this->case->id))
            ->line('We appreciate your time and we value your feedback!')
            ->salutation('Thank you.');
    }
}

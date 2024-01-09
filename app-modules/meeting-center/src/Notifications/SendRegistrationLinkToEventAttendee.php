<?php

namespace AdvisingApp\MeetingCenter\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use App\Models\NotificationSetting;
use Illuminate\Notifications\Notification;
use AdvisingApp\MeetingCenter\Models\Event;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use AdvisingApp\Notification\Notifications\Messages\MailMessage;

class SendRegistrationLinkToEventAttendee extends Notification
{
    use Queueable;

    public function __construct(
        protected Event $event,
        protected User $sender
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(EventAttendee $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(EventAttendee $notifiable): MailMessage
    {
        return MailMessage::make()
            ->settings($this->resolveNotificationSetting($notifiable))
            ->subject('You have been invited to an event!')
            ->line("You have been invited to {$this->event->title}.")
            ->action('Register', route('event-registration.show', ['event' => $this->event]));
    }

    private function resolveNotificationSetting(EventAttendee $notifiable): ?NotificationSetting
    {
        return $this->sender->teams()->first()?->division?->notificationSetting?->setting;
    }
}

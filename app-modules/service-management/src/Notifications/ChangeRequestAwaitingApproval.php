<?php

namespace AdvisingApp\ServiceManagement\Notifications;

use App\Models\User;
use App\Models\NotificationSetting;
use AdvisingApp\ServiceManagement\Models\ChangeRequest;
use AdvisingApp\Notification\Notifications\BaseNotification;
use AdvisingApp\Notification\Notifications\EmailNotification;
use AdvisingApp\Notification\Notifications\Messages\MailMessage;
use AdvisingApp\Notification\Models\Contracts\NotifiableInterface;
use AdvisingApp\Notification\Notifications\Concerns\EmailChannelTrait;
use AdvisingApp\ServiceManagement\Filament\Resources\ChangeRequestResource;

class ChangeRequestAwaitingApproval extends BaseNotification implements EmailNotification
{
    use EmailChannelTrait;

    public function __construct(
        public ChangeRequest $changeRequest,
    ) {}

    public function toEmail(NotifiableInterface $notifiable): MailMessage
    {
        return MailMessage::make()
            ->settings($this->resolveNotificationSetting($notifiable))
            ->subject('A Change Request is awaiting your approval')
            ->line("Hello {$notifiable->name}, the following Change Request is awaiting your approval:")
            ->line("{$this->changeRequest->title}")
            ->line("{$this->changeRequest->description}")
            ->line('You can view more details about this Change Request by clicking the button below.')
            ->action('View Change Request', url(ChangeRequestResource::getUrl('view', ['record' => $this->changeRequest])));
    }

    private function resolveNotificationSetting(User $notifiable): ?NotificationSetting
    {
        return $notifiable->teams()->first()?->division?->notificationSetting?->setting;
    }
}

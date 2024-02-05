<?php

namespace AdvisingApp\ServiceManagement\Notifications;

use AdvisingApp\Notification\Models\OutboundDeliverable;
use App\Models\NotificationSetting;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\ServiceManagement\Models\ServiceRequest;
use AdvisingApp\Notification\Notifications\BaseNotification;
use AdvisingApp\Notification\Notifications\EmailNotification;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\Notification\Notifications\Messages\MailMessage;
use AdvisingApp\Notification\Notifications\Concerns\EmailChannelTrait;

class SendEducatableServiceRequestClosedNotification extends BaseNotification implements EmailNotification
{
    use EmailChannelTrait;

    public function __construct(
        protected ServiceRequest $serviceRequest,
    ) {}

    public function toEmail(object $notifiable): MailMessage
    {
        /** @var Educatable $educatable */
        $educatable = $notifiable;

        $name = match ($notifiable::class) {
            Student::class => $educatable->first,
            Prospect::class => $educatable->first_name,
        };

        $status = $this->serviceRequest->status;

        return MailMessage::make()
            ->settings($this->resolveNotificationSetting($notifiable))
            ->subject("{$this->serviceRequest->service_request_number} - is now {$status->name}")
            ->greeting("Hello {$name},")
            ->line("Your request {$this->serviceRequest->service_request_number} for service is now {$status->name}.")
            ->line('Thank you.');
    }

    public function beforeSendHook(object $notifiable, OutboundDeliverable $deliverable, string $channel): void
    {
        $deliverable->update([
            'related_id' => $this->serviceRequest->getKey(),
            'related_type' => $this->serviceRequest->getMorphClass(),
        ]);
    }

    private function resolveNotificationSetting(object $notifiable): ?NotificationSetting
    {
        return $this->serviceRequest->division?->notificationSetting?->setting;
    }
}

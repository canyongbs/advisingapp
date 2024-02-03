<?php

namespace AdvisingApp\ServiceManagement\Notifications;

use App\Models\NotificationSetting;
use AdvisingApp\ServiceManagement\Models\ServiceRequest;
use AdvisingApp\Notification\Notifications\BaseNotification;
use AdvisingApp\Notification\Notifications\EmailNotification;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\ServiceManagement\Models\ServiceRequestStatus;
use AdvisingApp\Notification\Notifications\Messages\MailMessage;
use AdvisingApp\Notification\Notifications\Concerns\EmailChannelTrait;
use AdvisingApp\ServiceManagement\Enums\SystemServiceRequestClassification;

class SendEducatableServiceRequestOpenedNotification extends BaseNotification implements EmailNotification
{
    use EmailChannelTrait;

    public function __construct(
        protected ServiceRequest $serviceRequest,
    ) {}

    public function toEmail(object $notifiable): MailMessage
    {
        /** @var Educatable $educatable */
        $educatable = $notifiable;

        $status = ServiceRequestStatus::query()
            ->where('classification', SystemServiceRequestClassification::Open)
            ->value('name');

        $message = MailMessage::make()
            ->settings($this->resolveNotificationSetting($notifiable))
            ->subject("{$this->serviceRequest->service_request_number} is now {$status}")
            ->greeting("Hello {$educatable->first_name},");

        return $message;
    }

    private function resolveNotificationSetting(object $notifiable): ?NotificationSetting
    {
        return $this->serviceRequest->division?->notificationSetting?->setting;
    }
}

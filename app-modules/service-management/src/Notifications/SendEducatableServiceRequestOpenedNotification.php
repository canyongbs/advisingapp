<?php

namespace AdvisingApp\ServiceManagement\Notifications;

use AdvisingApp\ServiceManagement\Enums\ServiceRequestUpdateDirection;
use App\Models\NotificationSetting;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Notification\Models\OutboundDeliverable;
use AdvisingApp\ServiceManagement\Models\ServiceRequest;
use AdvisingApp\Notification\Notifications\BaseNotification;
use AdvisingApp\Notification\Notifications\EmailNotification;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\Notification\Notifications\Messages\MailMessage;
use AdvisingApp\Notification\Notifications\Concerns\EmailChannelTrait;

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

        $name = match ($notifiable::class) {
            Student::class => $educatable->first,
            Prospect::class => $educatable->first_name,
        };

        $status = $this->serviceRequest->status;
        $type = $this->serviceRequest->priority->type;

        return MailMessage::make()
            ->settings($this->resolveNotificationSetting($notifiable))
            ->subject("{$this->serviceRequest->service_request_number} - is now {$status->name}")
            ->greeting("Hello {$name},")
            ->line("A new {$type->name} service request has been created and is now in a {$status->name} status. Your new ticket number is: {$this->serviceRequest->service_request_number}.")
            ->line('The details of your service request are shown below:')
            ->lines(str(nl2br($this->serviceRequest->close_details))->explode('<br />'));
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

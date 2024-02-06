<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\ServiceManagement\Notifications;

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
            ->lines(str(nl2br($this->serviceRequest->close_details))->explode('<br />'))
            ->salutation('');
    }

    protected function beforeSendHook(object $notifiable, OutboundDeliverable $deliverable, string $channel): void
    {
        $deliverable->related()->associate($this->serviceRequest);
    }

    private function resolveNotificationSetting(object $notifiable): ?NotificationSetting
    {
        return $this->serviceRequest->division?->notificationSetting?->setting;
    }
}

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

namespace AdvisingApp\CaseManagement\Notifications;

use AdvisingApp\CaseManagement\Models\CaseModel;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Notification\Models\Contracts\NotifiableInterface;
use AdvisingApp\Notification\Models\OutboundDeliverable;
use AdvisingApp\Notification\Notifications\BaseNotification;
use AdvisingApp\Notification\Notifications\Concerns\EmailChannelTrait;
use AdvisingApp\Notification\Notifications\EmailNotification;
use AdvisingApp\Notification\Notifications\Messages\MailMessage;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\NotificationSetting;
use Illuminate\Notifications\AnonymousNotifiable;

class SendEducatableCaseOpenedNotification extends BaseNotification implements EmailNotification
{
    use EmailChannelTrait;

    public function __construct(
        protected CaseModel $case,
    ) {}

    public function toEmail(object $notifiable): MailMessage
    {
        /** @var Educatable $educatable */
        $educatable = $notifiable;

        $name = match ($notifiable::class) {
            Student::class => $educatable->first,
            Prospect::class => $educatable->first_name,
        };

        $status = $this->case->status;
        $type = $this->case->priority->type;

        return MailMessage::make()
            ->settings($this->resolveNotificationSetting($notifiable))
            ->subject("{$this->case->case_number} - is now {$status->name}")
            ->greeting("Hello {$name},")
            ->line("A new {$type->name} case has been created and is now in a {$status->name} status. Your new ticket number is: {$this->case->case_number}.")
            ->line('The details of your case are shown below:')
            ->lines(str(nl2br($this->case->close_details))->explode('<br />'));
    }

    public function beforeSend(AnonymousNotifiable|NotifiableInterface $notifiable, OutboundDeliverable $deliverable, NotificationChannel $channel): void
    {
        $deliverable->related()->associate($this->case);
    }

    private function resolveNotificationSetting(object $notifiable): ?NotificationSetting
    {
        return $this->case->division?->notificationSetting?->setting;
    }
}

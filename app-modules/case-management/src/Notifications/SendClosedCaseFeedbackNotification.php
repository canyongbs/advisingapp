<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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
use AdvisingApp\CaseManagement\Models\CaseTypeEmailTemplate;
use AdvisingApp\CaseManagement\Notifications\Concerns\HandlesCaseTemplateContent;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Notification\Models\Contracts\CanBeNotified;
use AdvisingApp\Notification\Models\Contracts\Message;
use AdvisingApp\Notification\Notifications\Contracts\HasBeforeSendHook;
use AdvisingApp\Notification\Notifications\Messages\MailMessage;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Models\NotificationSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Notification;

class SendClosedCaseFeedbackNotification extends Notification implements ShouldQueue, HasBeforeSendHook
{
    use Queueable;
    use HandlesCaseTemplateContent;

    public function __construct(
        protected CaseModel $case,
        public ?CaseTypeEmailTemplate $emailTemplate,
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
        $educatable = $notifiable;
        assert($educatable instanceof Educatable);

        $name = ($notifiable instanceof Student || $notifiable instanceof Prospect)
          ? $educatable->displayNameKey()
          : '';

        $template = $this->emailTemplate;

        if (! $template) {
            return MailMessage::make()
                ->settings($this->resolveNotificationSetting($notifiable))
                ->subject("Feedback survey for {$this->case->case_number}")
                ->greeting("Hi {$name},")
                ->line('To help us serve you better in the future, we’d love to hear about your experience with our support team.')
                ->action('Rate Service', route('feedback.case', $this->case->id))
                ->line('We appreciate your time and we value your feedback!')
                ->salutation('Thank you.');
        }

        $subject = $this->getSubject($template->subject);

        $body = $this->getBody($template->body);

        $test = MailMessage::make()
            ->settings($this->resolveNotificationSetting($notifiable))
            ->subject(strip_tags($subject))
            ->content($body);

        return $test;
    }

    public function beforeSend(AnonymousNotifiable|CanBeNotified $notifiable, Message $message, NotificationChannel $channel): void
    {
        $message->related()->associate($this->case);
    }

    private function resolveNotificationSetting(object $notifiable): ?NotificationSetting
    {
        return $this->case->division->notificationSetting?->setting;
    }
}

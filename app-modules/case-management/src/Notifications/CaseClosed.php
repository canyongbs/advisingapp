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

use AdvisingApp\CaseManagement\Filament\Resources\CaseResource;
use AdvisingApp\CaseManagement\Models\CaseModel;
use AdvisingApp\CaseManagement\Models\CaseTypeEmailTemplate;
use AdvisingApp\CaseManagement\Notifications\Concerns\HandlesCaseTemplateContent;
use AdvisingApp\Notification\Notifications\Channels\DatabaseChannel;
use AdvisingApp\Notification\Notifications\Channels\MailChannel;
use AdvisingApp\Notification\Notifications\Messages\MailMessage;
use App\Models\NotificationSetting;
use Filament\Notifications\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification as BaseNotification;

class CaseClosed extends BaseNotification implements ShouldQueue
{
    use Queueable;
    use HandlesCaseTemplateContent;

    /**
     * @param class-string $channel
     */
    public function __construct(
        public CaseModel $case,
        public ?CaseTypeEmailTemplate $emailTemplate,
        public string $channel,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [match ($this->channel) {
            DatabaseChannel::class => 'database',
            MailChannel::class => 'mail',
            default => '',
        }];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $template = $this->emailTemplate;

        if (! $template) {
            return MailMessage::make()
                ->settings($this->resolveNotificationSetting($notifiable))
                ->subject("Case {$this->case->case_number} closed")
                ->line("The Case {$this->case->case_number} has been closed.")
                ->action('View Case', CaseResource::getUrl('view', ['record' => $this->case]));
        }

        $subject = $this->getSubject($template->subject);

        $body = $this->getBody($template->body);

        return MailMessage::make()
            ->settings($this->resolveNotificationSetting($notifiable))
            ->subject(strip_tags($subject))
            ->content($body);
    }

    public function toDatabase(object $notifiable): array
    {
        return Notification::make()
            ->success()
            ->title((string) str("[Case {$this->case->case_number}](" . CaseResource::getUrl('view', ['record' => $this->case]) . ') closed')->markdown())
            ->getDatabaseMessage();
    }

    private function resolveNotificationSetting(object $notifiable): ?NotificationSetting
    {
        return $this->case->division->notificationSetting?->setting;
    }
}

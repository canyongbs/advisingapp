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

namespace AdvisingApp\Engagement\Notifications;

use App\Models\User;
use App\Models\NotificationSetting;
use AdvisingApp\Engagement\Models\EngagementBatch;
use AdvisingApp\Notification\Notifications\BaseNotification;
use AdvisingApp\Notification\Notifications\EmailNotification;
use AdvisingApp\Notification\Notifications\DatabaseNotification;
use AdvisingApp\Notification\Notifications\Messages\MailMessage;
use Filament\Notifications\Notification as FilamentNotification;
use AdvisingApp\Notification\Notifications\Concerns\EmailChannelTrait;
use AdvisingApp\Notification\Notifications\Concerns\DatabaseChannelTrait;

class EngagementBatchStartedNotification extends BaseNotification implements DatabaseNotification, EmailNotification
{
    use DatabaseChannelTrait;
    use EmailChannelTrait;

    private string $title;

    public function __construct(
        public EngagementBatch $engagementBatch,
        public int $jobsToProcess,
        public string $deliveryMethod,
    ) {
        $this->title = $this->deliveryMethod === 'email'
                  ? 'Bulk email request is being processed and will be sent shortly.'
                  : 'Bulk text request is being processed and will be sent shortly.';
    }

    public function toEmail(object $notifiable): MailMessage
    {
        return MailMessage::make()
            ->settings($this->resolveNotificationSetting($notifiable))
            ->subject($this->title)
            ->line("We've started processing your bulk engagement of {$this->jobsToProcess} jobs, and we'll keep you updated on the progress.");
    }

    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->status('success')
            ->title($this->title)
            ->body("{$this->jobsToProcess} jobs due for processing.")
            ->getDatabaseMessage();
    }

    private function resolveNotificationSetting(User $notifiable): ?NotificationSetting
    {
        return $this->engagementBatch->user->teams()->first()?->division?->notificationSetting?->setting;
    }
}

<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Engagement\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use App\Notifications\MailMessage;
use App\Models\NotificationSetting;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Assist\Engagement\Models\EngagementBatch;
use Filament\Notifications\Notification as FilamentNotification;

class EngagementBatchFinishedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public EngagementBatch $engagementBatch,
        public int $processedJobs,
        public int $failedJobs,
    ) {}

    public function via(User $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        $message = MailMessage::make()
            ->settings($this->resolveNotificationSetting($notifiable));

        if ($this->failedJobs > 0) {
            return $message
                ->subject('Bulk Engagements Finished Processing With Errors.')
                ->line("{$this->failedJobs} jobs failed out of {$this->processedJobs} total jobs.");
        }

        return $message
            ->subject('Bulk Engagements Finished Processing Successfully.')
            ->line("{$this->processedJobs} jobs processed successfully.");
    }

    public function toDatabase(User $notifiable): array
    {
        if ($this->failedJobs > 0) {
            return FilamentNotification::make()
                ->warning()
                ->title('Bulk Engagement processing finished, but some jobs failed')
                ->body("{$this->failedJobs} jobs failed out of {$this->processedJobs} total jobs.")
                ->getDatabaseMessage();
        }

        return FilamentNotification::make()
            ->success()
            ->title('Bulk Engagement processing finished')
            ->body("{$this->processedJobs} jobs processed successfully.")
            ->getDatabaseMessage();
    }

    private function resolveNotificationSetting(User $notifiable): ?NotificationSetting
    {
        return $this->engagementBatch->user->teams()->first()?->division?->notificationSetting?->setting;
    }
}

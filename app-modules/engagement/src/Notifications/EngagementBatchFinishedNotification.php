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

namespace AdvisingApp\Engagement\Notifications;

use AdvisingApp\Engagement\Models\EngagementBatch;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Notification\Notifications\Messages\MailMessage;
use App\Models\NotificationSetting;
use App\Models\User;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class EngagementBatchFinishedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public EngagementBatch $engagementBatch,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = MailMessage::make()
            ->settings($this->resolveNotificationSetting($notifiable));

        if ($this->engagementBatch->successful_engagements < $this->engagementBatch->total_engagements) {
            return $message
                ->subject(match ($this->engagementBatch->channel) {
                    NotificationChannel::Email => 'Bulk email has been processed with failures',
                    NotificationChannel::Sms => 'Bulk SMS has been processed with failures',
                    default => 'Bulk engagement has been processed with failures',
                })
                ->line(($this->engagementBatch->total_engagements - $this->engagementBatch->successful_engagements) . " engagements failed out of {$this->engagementBatch->total_engagements}.");
        }

        return $message
            ->subject(match ($this->engagementBatch->channel) {
                NotificationChannel::Email => 'Bulk email has been processed',
                NotificationChannel::Sms => 'Bulk SMS has been processed',
                default => 'Bulk engagement has been processed',
            })
            ->line("{$this->engagementBatch->total_engagements} engagements sent successfully.");
    }

    public function toDatabase(object $notifiable): array
    {
        if ($this->engagementBatch->successful_engagements < $this->engagementBatch->total_engagements) {
            return FilamentNotification::make()
                ->warning()
                ->title(match ($this->engagementBatch->channel) {
                    NotificationChannel::Email => 'Bulk email has been processed with failures',
                    NotificationChannel::Sms => 'Bulk SMS has been processed with failures',
                    default => 'Bulk engagement has been processed with failures',
                })
                ->body(($this->engagementBatch->total_engagements - $this->engagementBatch->successful_engagements) . " engagements failed out of {$this->engagementBatch->total_engagements}.")
                ->getDatabaseMessage();
        }

        return FilamentNotification::make()
            ->success()
            ->title(match ($this->engagementBatch->channel) {
                NotificationChannel::Email => 'Bulk email has been processed',
                NotificationChannel::Sms => 'Bulk SMS has been processed',
                default => 'Bulk engagement has been processed',
            })
            ->body("{$this->engagementBatch->total_engagements} engagements sent successfully.")
            ->getDatabaseMessage();
    }

    private function resolveNotificationSetting(User $notifiable): ?NotificationSetting
    {
        return $this->engagementBatch->user->team?->division?->notificationSetting?->setting;
    }
}

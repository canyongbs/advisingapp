<?php

namespace Assist\Engagement\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Assist\Engagement\Models\EngagementBatch;
use Illuminate\Notifications\Messages\MailMessage;
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

    public function toMail(object $notifiable): MailMessage
    {
        if ($this->failedJobs > 0) {
            return (new MailMessage())
                ->subject('Bulk Engagements Finished Processing With Errors.')
                ->line("{$this->failedJobs} jobs failed out of {$this->processedJobs} total jobs.");
        }

        return (new MailMessage())
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
}

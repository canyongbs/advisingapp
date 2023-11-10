<?php

namespace Assist\Engagement\Notifications;

use App\Models\User;
use App\Models\EmailTemplate;
use Assist\Engagement\Models\EngagementBatch;
use Illuminate\Bus\Queueable;
use App\Notifications\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Filament\Notifications\Notification as FilamentNotification;

class EngagementBatchStartedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public EngagementBatch $engagementBatch,
        public int $jobsToProcess,
    ) {}

    public function via(User $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return MailMessage::make()
            ->emailTemplate($this->resolveEmailTemplate())
            ->subject('Bulk Engagement processing started')
            ->line("We've started processing your bulk engagement of {$this->jobsToProcess} jobs, and we'll keep you updated on the progress.");
    }

    public function toDatabase(User $notifiable): array
    {
        return FilamentNotification::make()
            ->status('success')
            ->title('Bulk Engagement processing started')
            ->body("{$this->jobsToProcess} jobs due for processing.")
            ->getDatabaseMessage();
    }

    private function resolveEmailTemplate(): ?EmailTemplate
    {
        return $this->engagementBatch->user->teams()->first()?->division?->emailTemplate;
    }
}

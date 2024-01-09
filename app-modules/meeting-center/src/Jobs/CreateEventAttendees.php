<?php

namespace AdvisingApp\MeetingCenter\Jobs;

use App\Models\User;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Bus;
use Illuminate\Queue\SerializesModels;
use Filament\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;
use AdvisingApp\MeetingCenter\Models\Event;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CreateEventAttendees implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected Event $event,
        protected array $emails,
        protected User $sender,
    ) {}

    public function handle(): void
    {
        $sender = $this->sender;

        Bus::batch(collect($this->emails)->map(fn ($email) => new CreateEventAttendee($this->event, $email, $sender)))
            ->name("Invite Attendees to Event: {$this->event->getKey()}")
            ->finally(function (Batch $batch) use ($sender) {
                $successfulJobsCount = number_format($batch->totalJobs - $batch->failedJobs);

                $body = 'The'
                    . ' ' . str('invitation')->plural($batch->totalJobs)
                    . ' ' . ($batch->totalJobs > 1 ? 'have' : 'has')
                    . ' been sent and ' . $successfulJobsCount
                    . ' ' . ($successfulJobsCount == 1 ? 'email was' : 'emails were')
                    . ' successful.';

                if ($failedJobsCount = $batch->failedJobs) {
                    $body .= ' ' . number_format($failedJobsCount) . ' ' . str('email')->plural($failedJobsCount) . ' failed to send.';
                }

                Notification::make()
                    ->title($batch->totalJobs > 1 ? 'The invitations have been sent' : 'The invitation has been sent')
                    ->body($body)
                    ->when(
                        ! $failedJobsCount,
                        fn (Notification $notification) => $notification->success(),
                    )
                    ->when(
                        $failedJobsCount && ($failedJobsCount < $batch->totalJobs),
                        fn (Notification $notification) => $notification->warning(),
                    )
                    ->when(
                        $failedJobsCount === $batch->totalJobs,
                        fn (Notification $notification) => $notification->danger(),
                    )
                    ->sendToDatabase($sender);
            })
            ->dispatch();
    }
}

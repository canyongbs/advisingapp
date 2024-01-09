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
                if ($batch->hasFailures()) {
                    Notification::make()
                        ->warning()
                        ->title("{$batch->failedJobs} attendees failed to be invited.")
                        ->sendToDatabase($sender);
                } else {
                    Notification::make()
                        ->success()
                        ->title('All attendees have been invited.')
                        ->sendToDatabase($sender);
                }
            })
            ->dispatch();
    }
}

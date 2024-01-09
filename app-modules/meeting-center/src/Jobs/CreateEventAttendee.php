<?php

namespace AdvisingApp\MeetingCenter\Jobs;

use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use AdvisingApp\MeetingCenter\Models\Event;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use AdvisingApp\MeetingCenter\Enums\EventAttendeeStatus;
use AdvisingApp\MeetingCenter\Notifications\SendRegistrationLinkToEventAttendee;

class CreateEventAttendee implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use Batchable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected Event $event,
        protected string $email,
        protected User $sender
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        if ($this->event->attendees()->where('email', $this->email)->exists()) {
            $this->fail("{$this->email} has already been invited to this event.");

            return;
        }

        /** @var EventAttendee $attendee */
        $attendee = $this->event->attendees()->create([
            'email' => $this->email,
            'status' => EventAttendeeStatus::Invited,
        ]);

        $attendee->notify(new SendRegistrationLinkToEventAttendee($this->event, $this->sender));
    }
}

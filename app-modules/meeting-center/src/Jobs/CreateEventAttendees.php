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
    ) {
        $this->onQueue(config('meeting-center.queue'));
    }

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

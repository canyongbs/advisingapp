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

namespace AdvisingApp\Engagement\Jobs;

use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Engagement\Models\EngagementBatch;
use AdvisingApp\Engagement\Notifications\EngagementNotification;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Notification\Models\Contracts\CanBeNotified;
use DateTimeInterface;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimitedWithRedis;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class CreateBatchedEngagement implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $maxExceptions = 3;

    public function __construct(
        public EngagementBatch $engagementBatch,
        public CanBeNotified $recipient,
    ) {}

    /**
     * @return array<object>
     */
    public function middleware(): array
    {
        return [new RateLimitedWithRedis('notification')];
    }

    public function retryUntil(): DateTimeInterface
    {
        return now()->addHours(2);
    }

    public function handle(): void
    {
        $engagement = new Engagement();
        $engagement->engagementBatch()->associate($this->engagementBatch);
        $engagement->user()->associate($this->engagementBatch->user);
        $engagement->recipient()->associate($this->recipient);
        $engagement->channel = $this->engagementBatch->channel;
        $engagement->subject = $this->engagementBatch->subject;
        $engagement->body = $this->engagementBatch->body;
        $engagement->scheduled_at = $this->engagementBatch->scheduled_at;

        if (! $engagement->scheduled_at) {
            $engagement->dispatched_at = now();
        }

        DB::transaction(function () use ($engagement) {
            $engagement->save();

            if ($engagement->scheduled_at) {
                return;
            }

            if ($engagement->channel === NotificationChannel::Email && ! $engagement->recipient->canReceiveEmail()) {
                return;
            }

            if ($engagement->channel === NotificationChannel::Sms && ! $engagement->recipient->canReceiveSms()) {
                return;
            }

            $engagement->recipient->notifyNow(new EngagementNotification($engagement));
        });
    }
}

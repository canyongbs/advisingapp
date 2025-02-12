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

namespace AdvisingApp\Engagement\Actions;

use AdvisingApp\Engagement\DataTransferObjects\EngagementBatchCreationData;
use AdvisingApp\Engagement\DataTransferObjects\EngagementCreationData;
use AdvisingApp\Engagement\Jobs\CreateBatchedEngagement;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Engagement\Models\EngagementBatch;
use AdvisingApp\Engagement\Notifications\EngagementBatchFinishedNotification;
use AdvisingApp\Engagement\Notifications\EngagementBatchStartedNotification;
use AdvisingApp\Notification\Models\Contracts\CanBeNotified;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * @deprecated After deploying engagements refactor:
 * - Remove interface
 * - Remove all traits
 * - Remove constructor
 * - Remove `handle()` method, this is an action class and not a job.
 */
class CreateEngagementBatch implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public ?EngagementBatchCreationData $data = null,
    ) {}

    public function execute(EngagementCreationData $data): void
    {
        $engagementBatch = new EngagementBatch();
        $engagementBatch->user()->associate($data->user);
        $engagementBatch->channel = $data->channel;
        $engagementBatch->subject = $data->subject;
        $engagementBatch->scheduled_at = $data->scheduledAt;
        $engagementBatch->total_engagements = $data->recipient->count();
        $engagementBatch->processed_engagements = 0;
        $engagementBatch->successful_engagements = 0;

        DB::transaction(function () use ($engagementBatch, $data) {
            $engagementBatch->save();

            [$engagementBatch->body] = tiptap_converter()->saveImages(
                $data->body,
                disk: 's3-public',
                record: $engagementBatch,
                recordAttribute: 'body',
                newImages: $data->temporaryBodyImages,
            );

            $engagementBatch->save();
        });

        try {
            $batch = Bus::batch([
                ...blank($data->scheduledAt) ? [fn () => $engagementBatch->user->notify(new EngagementBatchStartedNotification($engagementBatch))] : [],
                ...$data->recipient
                    ->map(fn (CanBeNotified $recipient): CreateBatchedEngagement => new CreateBatchedEngagement($engagementBatch, $recipient))
                    ->all(),
            ])
                ->name("Bulk Engagement {$engagementBatch->getKey()}")
                ->finally(function () use ($engagementBatch) {
                    if ($engagementBatch->scheduled_at) {
                        return;
                    }

                    $engagementBatch->refresh();

                    $engagementBatch->user->notify(new EngagementBatchFinishedNotification($engagementBatch));
                })
                ->allowFailures()
                ->dispatch();

            $engagementBatch->identifier = $batch->id;
            $engagementBatch->save();
        } catch (Throwable $exception) {
            $engagementBatch->delete();

            throw $exception;
        }
    }

    public function handle(): void
    {
        /** @var EngagementBatch $engagementBatch */
        $engagementBatch = EngagementBatch::create([
            'user_id' => $this->data->user->id,
        ]);

        $channel = $this->data->channel;

        [$body] = tiptap_converter()->saveImages(
            $this->data->body,
            disk: 's3-public',
            record: $engagementBatch,
            recordAttribute: 'body',
            newImages: $this->data->temporaryBodyImages,
        );

        $engagements = $this->data->records->map(function (Student|Prospect $record) use ($body, $engagementBatch) {
            return $engagementBatch->engagements()->create([
                'user_id' => $engagementBatch->user_id,
                'recipient_id' => $record->identifier(),
                'recipient_type' => $record->getMorphClass(),
                'body' => $body,
                'subject' => $this->data->subject,
                'channel' => $this->data->channel,
            ]);
        });

        $deliverableJobs = $engagements->flatten()->map(function (Engagement $engagement) {
            return $engagement->driver()->jobForDelivery();
        });

        $engagementBatch->user->notify(new EngagementBatchStartedNotification($engagementBatch));

        Bus::batch($deliverableJobs)
            ->name("Process Bulk Engagement {$engagementBatch->id}")
            ->finally(function (Batch $batchQueue) use ($engagementBatch) {
                $engagementBatch->user->notify(new EngagementBatchFinishedNotification($engagementBatch));
            })
            ->allowFailures()
            ->dispatch();
    }
}

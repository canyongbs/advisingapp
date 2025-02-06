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

use AdvisingApp\Engagement\DataTransferObjects\EngagementCreationData;
use AdvisingApp\Engagement\Jobs\CreateBatchedEngagement;
use AdvisingApp\Engagement\Models\EngagementBatch;
use AdvisingApp\Engagement\Notifications\EngagementBatchFinishedNotification;
use AdvisingApp\Engagement\Notifications\EngagementBatchStartedNotification;
use AdvisingApp\Notification\Models\Contracts\CanBeNotified;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

class CreateEngagementBatch
{
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
        });
    }
}

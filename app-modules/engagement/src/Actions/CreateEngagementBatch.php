<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Engagement\Actions;

use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Bus;
use Assist\Prospect\Models\Prospect;
use Illuminate\Queue\SerializesModels;
use Assist\Engagement\Models\Engagement;
use Illuminate\Queue\InteractsWithQueue;
use Assist\AssistDataModel\Models\Student;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Assist\Engagement\Models\EngagementBatch;
use Assist\Engagement\Models\EngagementDeliverable;
use Assist\Engagement\DataTransferObjects\EngagementBatchCreationData;
use Assist\Engagement\Notifications\EngagementBatchStartedNotification;
use Assist\Engagement\Notifications\EngagementBatchFinishedNotification;

class CreateEngagementBatch implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public EngagementBatchCreationData $data
    ) {}

    public function handle(): void
    {
        $engagementBatch = EngagementBatch::create([
            'user_id' => $this->data->user->id,
        ]);

        $this->data->records->each(function (Student|Prospect $record) use ($engagementBatch) {
            /** @var Engagement $engagement */
            $engagement = $engagementBatch->engagements()->create([
                'user_id' => $engagementBatch->user_id,
                'recipient_id' => $record->identifier(),
                'recipient_type' => $record->getMorphClass(),
                'subject' => $this->data->subject,
                'body' => $this->data->body,
                'scheduled' => false,
            ]);

            $createDeliverablesForEngagement = resolve(CreateDeliverablesForEngagement::class);
            $createDeliverablesForEngagement($engagement, $this->data->deliveryMethod);
        });

        $deliverables = $engagementBatch->engagements->map(function (Engagement $engagement) {
            return $engagement->deliverable;
        });

        $deliverableJobs = $deliverables->flatten()->map(function (EngagementDeliverable $deliverable) {
            return $deliverable->jobForDelivery();
        });

        $engagementBatch->user->notify(new EngagementBatchStartedNotification($engagementBatch, $deliverableJobs->count()));

        Bus::batch($deliverableJobs)
            ->name("Process Bulk Engagement {$engagementBatch->id}")
            ->finally(function (Batch $batchQueue) use ($engagementBatch) {
                $engagementBatch->user->notify(new EngagementBatchFinishedNotification($engagementBatch, $batchQueue->totalJobs, $batchQueue->failedJobs));
            })
            ->allowFailures()
            ->dispatch();
    }
}

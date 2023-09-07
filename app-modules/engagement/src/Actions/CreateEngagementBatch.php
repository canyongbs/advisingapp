<?php

namespace Assist\Engagement\Actions;

use App\Models\User;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Assist\Prospect\Models\Prospect;
use Illuminate\Queue\SerializesModels;
use Assist\Engagement\Models\Engagement;
use Illuminate\Queue\InteractsWithQueue;
use Assist\AssistDataModel\Models\Student;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Assist\Engagement\Models\EngagementBatch;
use Assist\Engagement\Notifications\EngagementBatchFinishedNotification;

class CreateEngagementBatch implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public User $user,
        public Collection $records,
        public array $data,
    ) {}

    public function handle(): void
    {
        $engagementBatch = EngagementBatch::create([
            'user_id' => $this->user->id,
        ]);

        $this->records->each(function (Student|Prospect $record) use ($engagementBatch) {
            $engagement = $engagementBatch->engagements()->create([
                'user_id' => $engagementBatch->user_id,
                'recipient_id' => $record->identifier(),
                'recipient_type' => $record->getMorphClass(),
                'subject' => $this->data['subject'],
                'body' => $this->data['body'],
                // TODO Determine if we want to support future delivery for batches
                // 'deliver_at' => $data['deliver_at'],
            ]);

            // Create Deliverables for each Engagement
            $createDeliverablesForEngagement = resolve(CreateDeliverablesForEngagement::class);
            $createDeliverablesForEngagement($engagement, $this->data['delivery_methods']);
        });

        $deliverEngagementJobs = $engagementBatch->engagements->map(function (Engagement $engagement) {
            return new DeliverEngagement($engagement);
        });

        $batch = Bus::batch($deliverEngagementJobs)
            ->name('Process Bulk Engagement')
            ->finally(function (Batch $batchQueue) use ($engagementBatch) {
                $engagementBatch->user->notify(new EngagementBatchFinishedNotification($engagementBatch, $batchQueue->processedJobs(), $batchQueue->failedJobs));
            })
            ->allowFailures()
            ->dispatch();

        $engagementBatch->update([
            'job_batch_id' => $batch->id,
        ]);
    }
}

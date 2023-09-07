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
        ray('CreateEngagementBatch()');

        $engagementBatch = EngagementBatch::create([
            'user_id' => $this->user->id,
        ]);

        // Create Engagements for each record
        $this->records->each(function (Student|Prospect $record) use ($engagementBatch) {
            $engagement = $engagementBatch->engagements()->create([
                'user_id' => $engagementBatch->user_id,
                'recipient_id' => $record->identifier(),
                'recipient_type' => $record->getMorphClass(),
                'subject' => $this->data['subject'],
                'body' => $this->data['body'],
                // TODO Determine if we want to support future delivery fot batching
                // If so, we might need to carry the delivery time upwards to the batch itself
                // And have an independent process that picks up batch engagements similar
                // To how it currently picks up individual engagements
                // 'deliver_at' => $data['deliver_at'],
            ]);

            // Create Deliverables for each Engagement
            $createDeliverablesForEngagement = resolve(CreateDeliverablesForEngagement::class);
            $createDeliverablesForEngagement($engagement, $this->data['delivery_methods']);
        });

        $deliverEngagementJobs = $engagementBatch->engagements->map(function (Engagement $engagement) {
            return new DeliverEngagement($engagement);
        });

        // After we've done this, we'll want to dispatch the batch job
        $batch = Bus::batch($deliverEngagementJobs)
            ->finally(function (Batch $batchQueue) use ($engagementBatch) {
                // TODO Currently we have handling in place for sending notifications of success
                // Or a warning if at least one job in the batch failed
                // But, we might also want to be able to let users know if ALL jobs in a batch failed
                $engagementBatch->user->notify(new EngagementBatchFinishedNotification($engagementBatch, $batchQueue->processedJobs(), $batchQueue->failedJobs));
            })
            ->allowFailures()
            ->name('Process Bulk Engagement')
            ->dispatch();

        $engagementBatch->update([
            'job_batch_id' => $batch->id,
        ]);
    }
}

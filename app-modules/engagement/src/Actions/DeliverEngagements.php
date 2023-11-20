<?php

namespace Assist\Engagement\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Assist\Engagement\Models\Engagement;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Assist\Engagement\Models\EngagementDeliverable;

class DeliverEngagements implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    // TODO Add another indicator to the engagements table to represent an engagement being scheduled
    // This will allow us to scope this query down further, and prevent any overlap with sync dispatches
    public function handle(): void
    {
        Engagement::query()
            ->where('deliver_at', '<=', now())
            ->hasNotBeenDelivered()
            ->isNotPartOfABatch()
            ->cursor()
            ->each(function (Engagement $engagement) {
                $engagement->deliverables()->each(function (EngagementDeliverable $deliverable) {
                    $deliverable->deliver();
                });
            });
    }
}

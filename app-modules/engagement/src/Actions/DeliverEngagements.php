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

    public function handle(): void
    {
        Engagement::query()
            ->where('deliver_at', '<=', now())
            ->hasNotBeenDelivered()
            // TODO Need to add this to our test case
            ->isNotPartOfABatch()
            ->cursor()
            ->each(function (Engagement $engagement) {
                $engagement->deliverables()->each(function (EngagementDeliverable $deliverable) {
                    $deliverable->deliver();
                });
            });
    }
}

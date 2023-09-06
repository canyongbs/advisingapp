<?php

namespace Assist\Engagement\Actions;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Assist\Engagement\Models\Engagement;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Assist\Engagement\Models\EngagementDeliverable;

class DeliverEngagement implements ShouldQueue
{
    use Batchable;
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public Engagement $engagement
    ) {}

    public function handle(): void
    {
        ray('DeliverEngagement()');

        $this->engagement->deliverables()->each(function (EngagementDeliverable $deliverable) {
            $deliverable->deliver();
        });
    }
}

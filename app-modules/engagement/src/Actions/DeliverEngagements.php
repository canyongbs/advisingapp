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
        Engagement::where('send_at', '<=', now())->cursor()->each(function (Engagement $engagement) {
            $engagement->deliverables()->each(function (EngagementDeliverable $deliverable) {
                // TODO We need to figure out how we will determine if the deliverables were delivered successfully
                // This way, we can update the records accordingly and send notifications to the user that their engagement was successfully delivered
                // Or, let them know that something went wrong.
                $deliverable->send();
            });
        });
    }
}

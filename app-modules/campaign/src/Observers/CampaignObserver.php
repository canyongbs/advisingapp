<?php

namespace Assist\Campaign\Observers;

use Assist\Campaign\Models\Campaign;

class CampaignObserver
{
    public function creating(Campaign $campaign): void
    {
        if (is_null($campaign->user_id) && ! is_null(auth()->user())) {
            $campaign->user_id = auth()->user()->id;
        }
    }
}

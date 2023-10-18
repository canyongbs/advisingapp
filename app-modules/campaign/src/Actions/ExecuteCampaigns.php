<?php

namespace Assist\Campaign\Actions;

use Assist\Campaign\Models\Campaign;
use Assist\Campaign\Models\CampaignAction;
use Illuminate\Contracts\Queue\ShouldQueue;

class ExecuteCampaigns implements ShouldQueue
{
    public function handle(): void
    {
        Campaign::query()
            ->where('execute_at', '<=', now())
            ->hasNotBeenExecuted()
            ->cursor()
            ->each(function (Campaign $campaign) {
                $campaign->actions()->each(function (CampaignAction $action) {
                    $action->execute();
                });
            });
    }
}

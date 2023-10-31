<?php

namespace Assist\Campaign\Actions;

use Assist\Campaign\Models\CampaignAction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ExecuteCampaignActions implements ShouldQueue
{
    use Dispatchable;

    public function handle(): void
    {
        CampaignAction::query()
            ->where('execute_at', '<=', now())
            ->hasNotBeenExecuted()
            ->cursor()
            ->each(function (CampaignAction $action) {
                ExecuteCampaignAction::dispatch($action);
            });
    }
}

<?php

namespace Assist\Campaign\Actions;

use Assist\Campaign\Models\CampaignAction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ExecuteCampaignAction implements ShouldQueue
{
    use Dispatchable;

    public function __construct(
        public CampaignAction $action
    ) {}

    public function handle(): void
    {
        $this->action->execute();
    }
}

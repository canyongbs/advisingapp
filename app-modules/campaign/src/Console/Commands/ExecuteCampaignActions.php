<?php

namespace Assist\Campaign\Console\Commands;

use Illuminate\Console\Command;
use Assist\Campaign\Actions\ExecuteCampaignActions as ExecuteCampaignActionsJob;

class ExecuteCampaignActions extends Command
{
    protected $signature = 'campaign-actions:execute';

    protected $description = 'Execute campaign actions that are scheduled to be executed.';

    public function handle(): void
    {
        dispatch(new ExecuteCampaignActionsJob());
    }
}

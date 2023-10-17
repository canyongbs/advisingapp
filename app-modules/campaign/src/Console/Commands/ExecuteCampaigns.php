<?php

namespace Assist\Campaign\Console\Commands;

use Illuminate\Console\Command;
use Assist\Campaign\Actions\ExecuteCampaigns as ExecuteCampaignsJob;

class ExecuteCampaigns extends Command
{
    protected $signature = 'campaigns:execute';

    protected $description = 'Execute campaigns that are scheduled to be executed.';

    public function handle(): void
    {
        dispatch(new ExecuteCampaignsJob());
    }
}

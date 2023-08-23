<?php

namespace Assist\Engagement\Console\Commands;

use Illuminate\Console\Command;
use Assist\Engagement\Actions\DeliverEngagements as DeliverEngagementsJob;

class DeliverEngagements extends Command
{
    protected $signature = 'engagements:deliver';

    protected $description = 'Deliver all engagements that are ready to be sent';

    public function handle(): void
    {
        dispatch(new DeliverEngagementsJob());
    }
}

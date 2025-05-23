<?php

namespace AdvisingApp\Campaign\Jobs\Middleware;

use AdvisingApp\Campaign\Jobs\ExecuteCampaignActionOnEducatableJob;

class FailIfBatchCancelled
{
    /**
     * Process the job.
     *
     * @param  ExecuteCampaignActionOnEducatableJob  $job
     * @param  callable  $next
     *
     * @return mixed
     */
    public function handle(ExecuteCampaignActionOnEducatableJob $job, $next)
    {
        if (method_exists($job, 'batch') && $job->batch()?->cancelled()) {
            $job->actionEducatable->markFailed();

            return;
        }

        $next($job);
    }
}

<?php

namespace App\Jobs\Middleware;

class SkipIfNotLocal
{
    public function handle($job, $next): void
    {
        if (! app()->environment('local')) {
            return;
        }

        $next($job);
    }
}

<?php

namespace App\Jobs\Concerns;

use App\Multitenancy\Events\NewTenantSetupFailure;
use Illuminate\Support\Facades\Event;
use Throwable;

trait UsedDuringNewTenantSetup
{
    public function failed(?Throwable $exception): void
    {
        Event::dispatch(new NewTenantSetupFailure($this->tenant, $exception));

        if (app()->bound('sentry')) {
            app('sentry')->captureException($exception);
        }
    }
}

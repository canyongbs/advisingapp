<?php

namespace App\Queue;

use App\Models\Tenant;
use Illuminate\Queue\BackgroundQueue;
use Illuminate\Support\Facades\Concurrency;
use Illuminate\Support\Facades\Queue;

class TenantAwareBackgroundQueue extends BackgroundQueue
{
    /**
     * @param string|object $job
     * @param mixed $data
     * @param string|null $queue
     */
    public function push($job, $data = '', $queue = null): void
    {
        $tenantId = Tenant::current()?->getKey();
        $serializedJob = serialize($job);

        Concurrency::driver('process')->defer(
            function () use ($tenantId, $serializedJob, $data, $queue) {
                if ($tenantId) {
                    Tenant::find($tenantId)->makeCurrent();
                }

                $job = unserialize($serializedJob);

                Queue::connection('sync')->push($job, $data, $queue);
            }
        );
    }
}

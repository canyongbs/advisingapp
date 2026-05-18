<?php

namespace App\Queue\Connectors;

use App\Queue\TenantAwareBackgroundQueue;
use Illuminate\Queue\Connectors\ConnectorInterface;

class TenantAwareBackgroundConnector implements ConnectorInterface
{
    /**
     * @param array<mixed> $config
     */
    public function connect(array $config): TenantAwareBackgroundQueue
    {
        return new TenantAwareBackgroundQueue($config['after_commit'] ?? null);
    }
}

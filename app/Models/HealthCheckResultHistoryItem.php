<?php

namespace App\Models;

use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;
use Spatie\Health\Models\HealthCheckResultHistoryItem as SpatieHealthCheckResultHistoryItem;

/**
 * @mixin IdeHelperHealthCheckResultHistoryItem
 */
class HealthCheckResultHistoryItem extends SpatieHealthCheckResultHistoryItem
{
    use UsesTenantConnection;
}

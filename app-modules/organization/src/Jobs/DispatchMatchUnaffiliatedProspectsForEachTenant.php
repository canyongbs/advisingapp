<?php

namespace AdvisingApp\Organization\Jobs;

use App\Jobs\DispatchForEachTenant;
use App\Models\Tenant;

class DispatchMatchUnaffiliatedProspectsForEachTenant extends DispatchForEachTenant
{
    protected function jobForTenant(Tenant $tenant): ?object
    {
        return null;
    }
}

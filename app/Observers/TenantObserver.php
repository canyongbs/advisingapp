<?php

namespace App\Observers;

use App\Models\Tenant;

class TenantObserver
{
    public function created(Tenant $tenant) {}
}

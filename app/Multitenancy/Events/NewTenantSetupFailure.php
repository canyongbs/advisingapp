<?php

namespace App\Multitenancy\Events;

use Throwable;
use App\Models\Tenant;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class NewTenantSetupFailure
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public Tenant $tenant,
        public Throwable $exception,
    ) {}
}

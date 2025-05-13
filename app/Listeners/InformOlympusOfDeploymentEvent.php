<?php

namespace App\Listeners;

use App\Multitenancy\Events\NewTenantSetupComplete;
use App\Services\Olympus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\Multitenancy\Jobs\NotTenantAware;

class InformOlympusOfDeploymentEvent implements ShouldQueue, NotTenantAware
{
    public function handle(NewTenantSetupComplete $event): void
    {
        $tenantId = $event->tenant->getKey();

        app(Olympus::class)->makeRequest()
            ->asJson()
            ->post("/api/deployment/{$tenantId}/status-event-inform", [
                'type' => 'deployment_complete',
                'occurred_at' => now()->toDateTimeString('millisecond'),
            ])
            ->throw();
    }
}

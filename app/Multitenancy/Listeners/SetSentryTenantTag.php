<?php

namespace App\Multitenancy\Listeners;

use Sentry\State\Scope;

use function Sentry\configureScope;

use Spatie\Multitenancy\Events\MakingTenantCurrentEvent;

class SetSentryTenantTag
{
    public function handle(MakingTenantCurrentEvent $event): void
    {
        configureScope(function (Scope $scope) use ($event): void {
            $scope->setTags([
                'tenant.id' => $event->tenant->getKey(),
                'tenant.name' => $event->tenant->name,
            ]);
        });
    }
}

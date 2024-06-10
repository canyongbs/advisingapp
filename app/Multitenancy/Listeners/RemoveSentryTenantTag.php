<?php

namespace App\Multitenancy\Listeners;

use Sentry\State\Scope;

use function Sentry\configureScope;

use Spatie\Multitenancy\Events\ForgotCurrentTenantEvent;

class RemoveSentryTenantTag
{
    public function handle(ForgotCurrentTenantEvent $event): void
    {
        configureScope(function (Scope $scope): void {
            $scope
                ->removeTag('tenant.id')
                ->removeTag('tenant.name');
        });
    }
}

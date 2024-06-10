<?php

namespace App\Listeners;

use Sentry\State\Scope;

use function Sentry\configureScope;

class ClearSentryUser
{
    public function handle(object $event): void
    {
        configureScope(function (Scope $scope): void {
            $scope->removeUser();
        });
    }
}

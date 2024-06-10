<?php

namespace App\Listeners;

use Sentry\State\Scope;
use App\Models\Authenticatable;
use Illuminate\Auth\Events\Login;

use function Sentry\configureScope;

use Illuminate\Auth\Events\Authenticated;

class SetSentryUser
{
    public function handle(Login|Authenticated $event): void
    {
        /** @var Authenticatable $user */
        $user = $event->user;

        if (filled($user)) {
            configureScope(function (Scope $scope) use ($user): void {
                $scope->setUser([
                    'id' => $user->getKey(),
                ]);
            });
        }
    }
}

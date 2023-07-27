<?php

namespace Assist\Prospect\Providers;

use Illuminate\Support\ServiceProvider;
use App\Actions\Finders\ApplicationModules;
use Assist\Authorization\AuthorizationRegistry;

class ProspectServiceProvider extends ServiceProvider
{
    public function register()
    {
        // TODO Register any models with the Authorization service
        // Register any roles with the Authorization service
    }

    public function boot(AuthorizationRegistry $registry)
    {
        $registry->registerWebPermissions(
            'prospect',
            [
                ...resolve(ApplicationModules::class)
                    ->moduleConfig(
                        module: 'prospect',
                        path: 'permissions/web/custom'
                    ),
            ]
        );

        $registry->registerApiPermissions(
            'prospect',
            [
                ...resolve(ApplicationModules::class)
                    ->moduleConfig(
                        module: 'prospect',
                        path: 'permissions/api/custom'
                    ),
            ]
        );
    }
}

<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\Providers;

use Closure;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        Route::macro('api', function (int $majorVersion, Closure $routes) {
            Route::middleware([
                'api',
                'auth:sanctum',
                'abilities:api',
            ])
                ->prefix("api/v{$majorVersion}")
                ->name("api.v{$majorVersion}.")
                ->scopeBindings()
                ->group($routes);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerApi(majorVersion: 1);

        Scramble::configure()
            ->expose(ui: false, document: false);
    }

    protected function registerApi(int $majorVersion): void
    {
        Scramble::registerApi("v{$majorVersion}", [
            'api_path' => "api/v{$majorVersion}",
            'info' => [
                'version' => "{$majorVersion}.0.0",
            ],
        ])
            ->expose(
                ui: "/docs/api/v{$majorVersion}",
                document: "/docs/api/v{$majorVersion}.json",
            )
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('bearer')
                        ->setDescription('You can issue a bearer token by visiting the "User Management > Programmatic Users" section of the app, and creating a new programmatic user. Once created, you will be able to see a generated API key for the user.'),
                );
            });
    }
}

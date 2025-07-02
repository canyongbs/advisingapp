<?php

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

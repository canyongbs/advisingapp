<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use OpenSearch\Migrations\Filesystem\MigrationStorage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        resolve(MigrationStorage::class)->registerPaths([
            'app-modules/prospect/opensearch/migrations',
        ]);
    }
}

<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\DataMigration\Providers;

use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use AdvisingApp\DataMigration\DataMigrationPlugin;
use App\Registries\RoleBasedAccessControlRegistry;
use Illuminate\Database\Eloquent\Relations\Relation;
use AdvisingApp\DataMigration\Registries\DataMigrationRbacRegistry;
use TimoKoerber\LaravelOneTimeOperations\Commands\OneTimeOperationShowCommand;
use TimoKoerber\LaravelOneTimeOperations\Commands\OneTimeOperationsMakeCommand;
use TimoKoerber\LaravelOneTimeOperations\Commands\OneTimeOperationsProcessCommand;

class DataMigrationServiceProvider extends ServiceProvider
{
    public function register()
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->getId() !== 'admin' || $panel->plugin(new DataMigrationPlugin()));

        $this->mergeConfigFrom(
            __DIR__ . '/../../config/one-time-operations.php',
            'one-time-operations'
        );
    }

    public function boot()
    {
        Relation::morphMap([]);

        RoleBasedAccessControlRegistry::register(DataMigrationRbacRegistry::class);

        $this->loadMigrationsFrom([__DIR__ . '/../../database/migrations']);

        $this->publishes([
            __DIR__ . '/../../config/one-time-operations.php' => config_path('one-time-operations.php'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands(OneTimeOperationsMakeCommand::class);
            $this->commands(OneTimeOperationsProcessCommand::class);
            $this->commands(OneTimeOperationShowCommand::class);
        }
    }
}

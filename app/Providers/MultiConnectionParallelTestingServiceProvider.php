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
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Testing\Concerns\TestDatabases;
use PHPUnit\Framework\TestCase;

/**
 * @class MultiConnectionParallelTestingServiceProvider
 *
 * Based on https://sarahjting.com/blog/laravel-paratest-multiple-db-connections
 */
class MultiConnectionParallelTestingServiceProvider extends ServiceProvider
{
    use TestDatabases;

    protected array $parallelConnections = ['tenant'];

    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('testing')) {
            ParallelTesting::setUpProcess(function (int $token) {
                $this->whenNotUsingInMemoryDatabase(function ($database) {
                    if (ParallelTesting::option('recreate_databases')) {
                        foreach ($this->parallelConnections as $connection) {
                            Schema::connection($connection)
                                ->dropDatabaseIfExists($this->databaseOnConnection($connection));
                        }
                    }
                });
            });

            ParallelTesting::setUpTestCase(function (int $token, TestCase $testCase) {
                $uses = array_flip(class_uses_recursive(get_class($testCase)));

                $databaseTraits = [
                    DatabaseMigrations::class,
                    DatabaseTransactions::class,
                    DatabaseTruncation::class,
                    RefreshDatabase::class,
                ];

                if (Arr::hasAny($uses, $databaseTraits) && ! ParallelTesting::option('without_databases')) {
                    $this->whenNotUsingInMemoryDatabase(function ($database) use ($uses) {
                        $allCreated = [];

                        foreach ($this->parallelConnections as $connection) {
                            $this->usingConnection($connection, function ($connection) use (&$allCreated) {
                                [$testDatabase, $created] = $this->ensureTestDatabaseExists($this->databaseOnConnection($connection));

                                $this->switchToDatabase($testDatabase);

                                if ($created) {
                                    $allCreated[] = [$connection, $testDatabase];
                                }
                            });
                        }

                        if (isset($uses[DatabaseTransactions::class])) {
                            $this->ensureSchemaIsUpToDate();
                        }

                        foreach ($allCreated as [$connection, $testDatabase]) {
                            $this->usingConnection($connection, function () use ($testDatabase) {
                                ParallelTesting::callSetUpTestDatabaseCallbacks($testDatabase);
                            });

                            Config::set("database.connections.{$connection}.database", $testDatabase);
                        }
                    });
                }
            });

            ParallelTesting::tearDownProcess(function () {
                $this->whenNotUsingInMemoryDatabase(function ($database) {
                    if (ParallelTesting::option('drop_databases')) {
                        foreach ($this->parallelConnections as $connection) {
                            Schema::connection($connection)
                                ->dropDatabaseIfExists($this->databaseOnConnection($connection));
                        }
                    }
                });
            });
        }
    }

    protected function usingConnection(string $connection, Closure $callable): void
    {
        $originalConnection = config('database.default');

        try {
            config()->set('database.default', $connection);
            $callable($connection);
        } finally {
            config()->set('database.default', $originalConnection);
        }
    }

    protected function databaseOnConnection(string $connection): string
    {
        return config("database.connections.{$connection}.database");
    }
}

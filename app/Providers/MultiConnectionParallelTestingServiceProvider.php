<?php

namespace App\Providers;

use Closure;
use Illuminate\Support\Arr;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Testing\Concerns\TestDatabases;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * @class MultiConnectionParallelTestingServiceProvider
 *
 * Based on https://sarahjting.com/blog/laravel-paratest-multiple-db-connections
 */
class MultiConnectionParallelTestingServiceProvider extends ServiceProvider
{
    use TestDatabases;

    protected array $parallelConnections = [
        [
            'connection' => 'sis',
            'database' => 'testing',
        ],
    ];

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
                            Schema::connection($connection['connection'])
                                ->dropDatabaseIfExists($connection['database']);
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
                    $this->whenNotUsingInMemoryDatabase(function ($database) use ($uses, $token) {
                        $allCreated = [];

                        foreach ($this->parallelConnections as $connection) {
                            $this->usingConnection($connection, function ($connection) use (&$allCreated) {
                                [$testDatabase, $created] = $this->ensureTestDatabaseExists($connection['database']);

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
                        }

                        Config::set('database.fdw.external_database', "testing_test_{$token}");
                    });
                }
            });

            ParallelTesting::tearDownProcess(function () {
                $this->whenNotUsingInMemoryDatabase(function ($database) {
                    if (ParallelTesting::option('drop_databases')) {
                        foreach ($this->parallelConnections as $connection) {
                            Schema::connection($connection['connection'])
                                ->dropDatabaseIfExists($connection['database']);
                        }
                    }
                });
            });
        }
    }

    protected function usingConnection(array $connection, Closure $callable): void
    {
        $originalConnection = config('database.default');

        try {
            config()->set('database.default', $connection['connection']);
            $callable($connection);
        } finally {
            config()->set('database.default', $originalConnection);
        }
    }
}

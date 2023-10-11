<?php

namespace App\Providers;

use Closure;
use Illuminate\Support\Arr;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Doctrine\DBAL\Query\QueryException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * @class MultiConnectionParallelTestingServiceProvider
 *
 * Based on vendor/laravel/framework/src/Illuminate/Testing/Concerns/TestDatabases.php and https://sarahjting.com/blog/laravel-paratest-multiple-db-connections
 */
class MultiConnectionParallelTestingServiceProvider extends ServiceProvider
{
    protected static bool $schemaIsUpToDate = false;

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
                    $this->whenNotUsingInMemoryDatabase(function ($database) use ($uses) {
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
                    });
                }

                ParallelTesting::tearDownProcess(function (int $token) {
                    $this->whenNotUsingInMemoryDatabase(function ($database) {
                        foreach ($this->parallelConnections as $connection) {
                            Schema::connection($connection['connection'])
                                ->dropDatabaseIfExists($connection['database']);
                        }
                    });
                });
            });
        }
    }

    /**
     * Ensure a test database exists and returns its name.
     *
     * @param  string  $database
     *
     * @return array
     */
    protected function ensureTestDatabaseExists($database)
    {
        $testDatabase = $this->testDatabase($database);

        try {
            $this->usingDatabase($testDatabase, function () {
                Schema::hasTable('dummy');
            });
        } catch (QueryException) {
            $this->usingDatabase($database, function () use ($testDatabase) {
                Schema::dropDatabaseIfExists($testDatabase);
                Schema::createDatabase($testDatabase);
            });

            return [$testDatabase, true];
        }

        return [$testDatabase, false];
    }

    /**
     * Ensure the current database test schema is up to date.
     *
     * @return void
     */
    protected function ensureSchemaIsUpToDate()
    {
        if (! static::$schemaIsUpToDate) {
            Artisan::call('migrate');

            static::$schemaIsUpToDate = true;
        }
    }

    /**
     * Runs the given callable using the given database.
     *
     * @param  string  $database
     * @param  callable  $callable
     *
     * @return void
     */
    protected function usingDatabase($database, $callable)
    {
        $original = DB::getConfig('database');

        try {
            $this->switchToDatabase($database);
            $callable();
        } finally {
            $this->switchToDatabase($original);
        }
    }

    /**
     * Apply the given callback when tests are not using in memory database.
     *
     * @param  callable  $callback
     *
     * @return void
     */
    protected function whenNotUsingInMemoryDatabase($callback)
    {
        if (ParallelTesting::option('without_databases')) {
            return;
        }

        $database = DB::getConfig('database');

        if ($database !== ':memory:') {
            $callback($database);
        }
    }

    /**
     * Switch to the given database.
     *
     * @param  string  $database
     *
     * @return void
     */
    protected function switchToDatabase($database)
    {
        DB::purge();

        $default = config('database.default');

        $url = config("database.connections.{$default}.url");

        if ($url) {
            config()->set(
                "database.connections.{$default}.url",
                preg_replace('/^(.*)(\/[\w-]*)(\??.*)$/', "$1/{$database}$3", $url),
            );
        } else {
            config()->set(
                "database.connections.{$default}.database",
                $database,
            );
        }
    }

    /**
     * Returns the test database name.
     *
     * @param mixed $database
     *
     * @return string
     */
    protected function testDatabase($database)
    {
        $token = ParallelTesting::token();

        return "{$database}_test_{$token}";
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

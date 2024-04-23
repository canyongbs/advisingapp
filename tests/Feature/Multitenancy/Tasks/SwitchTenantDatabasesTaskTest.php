<?php

use App\Models\Tenant;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertNotEquals;

beforeEach(function () {
    Tenant::forgetCurrent();
});

it('switches the tenant database', function () {
    /** @phpstan-ignore-next-line */
    $connectionName = $this->landlordDatabaseConnectionName();

    $before = config()->getMany([
        "database.connections.{$connectionName}.host",
        "database.connections.{$connectionName}.port",
        "database.connections.{$connectionName}.database",
        "database.connections.{$connectionName}.username",
        "database.connections.{$connectionName}.password",
        'database.default',
        'queue.failed.database',
    ]);

    Tenant::first()->makeCurrent();

    /** @phpstan-ignore-next-line */
    $connectionName = $this->tenantDatabaseConnectionName();

    $after = config()->getMany([
        "database.connections.{$connectionName}.host",
        "database.connections.{$connectionName}.port",
        "database.connections.{$connectionName}.database",
        "database.connections.{$connectionName}.username",
        "database.connections.{$connectionName}.password",
        'database.default',
        'queue.failed.database',
    ]);

    assertNotEquals($before, $after);

    Tenant::forgetCurrent();

    /** @phpstan-ignore-next-line */
    $connectionName = $this->landlordDatabaseConnectionName();

    $after = config()->getMany([
        "database.connections.{$connectionName}.host",
        "database.connections.{$connectionName}.port",
        "database.connections.{$connectionName}.database",
        "database.connections.{$connectionName}.username",
        "database.connections.{$connectionName}.password",
        'database.default',
        'queue.failed.database',
    ]);

    assertEquals($before, $after);
});

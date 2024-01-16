<?php

namespace App\Multitenancy\Tasks;

use Illuminate\Support\Facades\DB;
use Spatie\Multitenancy\Models\Tenant;
use Illuminate\Database\Eloquent\Model;
use Spatie\Multitenancy\Tasks\SwitchTenantTask;
use Spatie\Multitenancy\Concerns\UsesMultitenancyConfig;
use Spatie\Multitenancy\Exceptions\InvalidConfiguration;

class SwitchTenantDatabasesTask implements SwitchTenantTask
{
    use UsesMultitenancyConfig;

    public function __construct(
        protected ?string $tenantConnectionName = null,
        protected ?string $originalDbHost = null,
        protected ?string $originalDbPort = null,
        protected ?string $originalDbDatabase = null,
        protected ?string $originalDbUsername = null,
        protected ?string $originalDbPassword = null,
        protected ?string $originalSisDbHost = null,
        protected ?string $originalSisDbPort = null,
        protected ?string $originalSisDbDatabase = null,
        protected ?string $originalSisDbUsername = null,
        protected ?string $originalSisDbPassword = null,
    ) {
        $this->tenantConnectionName ??= $this->tenantDatabaseConnectionName();

        $this->ensureTenantConnectionIsValid($this->tenantConnectionName);

        $this->originalDbHost ??= config("database.connections.{$this->tenantConnectionName}.host");

        $this->originalDbPort ??= config("database.connections.{$this->tenantConnectionName}.port");

        $this->originalDbDatabase ??= config("database.connections.{$this->tenantConnectionName}.database");

        $this->originalDbUsername ??= config("database.connections.{$this->tenantConnectionName}.username");

        $this->originalDbPassword ??= config("database.connections.{$this->tenantConnectionName}.password");

        $this->originalSisDbHost ??= config('database.connections.sis.host');

        $this->originalSisDbPort ??= config('database.connections.sis.port');

        $this->originalSisDbDatabase ??= config('database.connections.sis.database');

        $this->originalSisDbUsername ??= config('database.connections.sis.username');

        $this->originalSisDbPassword ??= config('database.connections.sis.password');
    }

    public function makeCurrent(Tenant $tenant): void
    {
        $this->setTenantDatabase(
            connectionName: $this->tenantConnectionName,
            host: $tenant->db_host,
            port: $tenant->db_port,
            database: $tenant->database,
            username: $tenant->db_username,
            password: $tenant->db_password,
        );

        $this->setTenantDatabase(
            connectionName: 'sis',
            host: $tenant->sis_db_host,
            port: $tenant->sis_db_port,
            database: $tenant->sis_database,
            username: $tenant->sis_db_username,
            password: $tenant->sis_db_password,
        );

        config([
            'database.default' => $this->tenantConnectionName,
            'queue.failed.database' => $this->tenantConnectionName,
        ]);

        // Octane will have an old `db` instance in the Model::$resolver.
        Model::setConnectionResolver(app('db'));
    }

    public function forgetCurrent(): void
    {
        $this->setTenantDatabase(
            connectionName: $this->tenantConnectionName,
            host: $this->originalDbHost,
            port: $this->originalDbPort,
            database: $this->originalDbDatabase,
            username: $this->originalDbUsername,
            password: $this->originalDbPassword,
        );

        $this->setTenantDatabase(
            connectionName: 'sis',
            host: $this->originalSisDbHost,
            port: $this->originalSisDbPort,
            database: $this->originalSisDbDatabase,
            username: $this->originalSisDbUsername,
            password: $this->originalSisDbPassword,
        );

        config([
            'database.default' => 'landlord',
            'queue.failed.database' => 'landlord',
        ]);

        // Octane will have an old `db` instance in the Model::$resolver.
        Model::setConnectionResolver(app('db'));
    }

    public function ensureTenantConnectionIsValid(?string $tenantConnectionName): void
    {
        if ($tenantConnectionName === $this->landlordDatabaseConnectionName()) {
            throw InvalidConfiguration::tenantConnectionIsEmptyOrEqualsToLandlordConnection();
        }

        if (is_null(config("database.connections.{$tenantConnectionName}"))) {
            throw InvalidConfiguration::tenantConnectionDoesNotExist($tenantConnectionName);
        }
    }

    protected function setTenantDatabase(
        string $connectionName,
        ?string $host,
        ?string $port,
        ?string $database,
        ?string $username,
        ?string $password,
    ): void {
        config([
            "database.connections.{$connectionName}.host" => $host,
            "database.connections.{$connectionName}.port" => $port,
            "database.connections.{$connectionName}.database" => $database,
            "database.connections.{$connectionName}.username" => $username,
            "database.connections.{$connectionName}.password" => $password,
        ]);

        app('db')->extend($connectionName, fn ($config, $name) => app('db.factory')->make(
            array_merge(
                $config,
                [
                    'host' => $host,
                    'port' => $port,
                    'database' => $database,
                    'username' => $username,
                    'password' => $password,
                ]
            ),
            $name
        ));

        DB::purge($connectionName);
    }
}

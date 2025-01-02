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

namespace App\Multitenancy\Tasks;

use Illuminate\Bus\BatchRepository;
use Illuminate\Bus\DatabaseBatchRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\Multitenancy\Concerns\UsesMultitenancyConfig;
use Spatie\Multitenancy\Exceptions\InvalidConfiguration;
use Spatie\Multitenancy\Models\Tenant;
use Spatie\Multitenancy\Tasks\SwitchTenantTask;

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
    ) {
        $this->tenantConnectionName ??= $this->tenantDatabaseConnectionName();

        $this->ensureTenantConnectionIsValid($this->tenantConnectionName);

        $this->originalDbHost ??= config("database.connections.{$this->tenantConnectionName}.host");

        $this->originalDbPort ??= config("database.connections.{$this->tenantConnectionName}.port");

        $this->originalDbDatabase ??= config("database.connections.{$this->tenantConnectionName}.database");

        $this->originalDbUsername ??= config("database.connections.{$this->tenantConnectionName}.username");

        $this->originalDbPassword ??= config("database.connections.{$this->tenantConnectionName}.password");
    }

    public function makeCurrent(Tenant $tenant): void
    {
        $config = $tenant->config;

        $this->setTenantDatabase(
            connectionName: $this->tenantConnectionName,
            host: $config->database->host,
            port: $config->database->port,
            database: $config->database->database,
            username: $config->database->username,
            password: $config->database->password,
        );

        config([
            'database.default' => $this->tenantConnectionName,
            'queue.failed.database' => $this->tenantConnectionName,
        ]);

        // Octane will have an old `db` instance in the Model::$resolver.
        Model::setConnectionResolver(app('db'));

        app()->forgetInstance(DatabaseBatchRepository::class);
        app()->forgetInstance(BatchRepository::class);
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

        config([
            'database.default' => 'landlord',
            'queue.failed.database' => 'landlord',
        ]);

        // Octane will have an old `db` instance in the Model::$resolver.
        Model::setConnectionResolver(app('db'));

        app()->forgetInstance(DatabaseBatchRepository::class);
        app()->forgetInstance(BatchRepository::class);
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
